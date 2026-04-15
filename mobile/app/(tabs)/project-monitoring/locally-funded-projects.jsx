import { Feather } from "@expo/vector-icons";
import { useMemo, useState } from "react";
import {
  ActivityIndicator,
  FlatList,
  Pressable,
  RefreshControl,
  Text,
  TextInput,
  View,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { useDebounce } from "../../../hooks/useDebounce";
import {
  useLocallyFundedProjects,
} from "../../../hooks/useLocallyFundedProjects";

function escapeRegExp(value) {
  return String(value).replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
}

function HighlightedText({
  text,
  query,
  className,
  highlightClassName,
  numberOfLines,
  style,
  highlightStyle,
}) {
  const source = String(text ?? "");
  const keyword = String(query ?? "").trim();

  if (!keyword) {
    return (
      <Text className={className} numberOfLines={numberOfLines} style={style}>
        {source}
      </Text>
    );
  }

  const expression = new RegExp(`(${escapeRegExp(keyword)})`, "ig");
  const segments = source.split(expression);

  return (
    <Text className={className} numberOfLines={numberOfLines} style={style}>
      {segments.map((segment, index) => {
        const isMatch = segment.toLowerCase() === keyword.toLowerCase();

        if (!isMatch) {
          return segment;
        }

        return (
          <Text
            key={`${segment}-${index}`}
            className={highlightClassName}
            style={[style, highlightStyle]}
          >
            {segment}
          </Text>
        );
      })}
    </Text>
  );
}

export default function LocallyFundedProjectsScreen() {
  const { projects, isLoading, isRefreshing, errorMessage, loadProjects } =
    useLocallyFundedProjects();
  const [searchQuery, setSearchQuery] = useState("");
  const debouncedSearchQuery = useDebounce(searchQuery, 350);

  const filteredProjects = useMemo(() => {
    const keyword = debouncedSearchQuery.trim().toLowerCase();

    if (!keyword) {
      return projects;
    }

    return projects.filter((project) => {
      const fields = [
        project.code,
        project.title,
        project.city,
        project.province,
        project.fundSource,
        project.statusActual,
        project.statusSubaybayan,
      ];

      return fields.some((field) =>
        String(field || "").toLowerCase().includes(keyword)
      );
    });
  }, [debouncedSearchQuery, projects]);

  const highlightedQuery = debouncedSearchQuery.trim();

  const renderProjectCard = ({ item }) => {
    return (
      <View className="mb-3 rounded-3xl border border-[#bfc3c9] bg-[#ebebeb] px-3 py-3">
        <View className="flex-row items-start">
          <View className="flex-1 pr-2">
            <HighlightedText
              text={item.code}
              query={highlightedQuery}
              className="text-[15px] text-[#404040]"
              highlightClassName="rounded-sm bg-[#fde68a] text-[#1f2937]"
              style={{ fontFamily: "Montserrat-SemiBold" }}
              highlightStyle={{ fontFamily: "Montserrat-SemiBold" }}
              numberOfLines={1}
            />
            <HighlightedText
              text={item.title}
              query={highlightedQuery}
              className="mt-1 text-[12px] text-[#4b4b4b]"
              highlightClassName="rounded-sm bg-[#fde68a] text-[#1f2937]"
              style={{ fontFamily: "Montserrat" }}
              highlightStyle={{ fontFamily: "Montserrat" }}
            />
            <View className="mt-2 border-b border-[#bfc3c9]" />
            <Text className="mt-2 text-[12px] text-[#4b4b4b]">
              <HighlightedText
                text={item.city}
                query={highlightedQuery}
                className="text-[12px] text-[#4b4b4b]"
                highlightClassName="rounded-sm bg-[#fde68a] text-[#1f2937]"
                style={{ fontFamily: "Montserrat" }}
                highlightStyle={{ fontFamily: "Montserrat" }}
              />
              {", "}
              <HighlightedText
                text={item.province}
                query={highlightedQuery}
                className="text-[12px] text-[#4b4b4b]"
                highlightClassName="rounded-sm bg-[#fde68a] text-[#1f2937]"
                style={{ fontFamily: "Montserrat" }}
                highlightStyle={{ fontFamily: "Montserrat" }}
              />
            </Text>
          </View>
        </View>
      </View>
    );
  };

  return (
    <SafeAreaView className="flex-1 bg-[#f1f5f9]" edges={[]}>
      {/* <View className="px-4 pt-4 pb-2">
        <Text className="text-[23px] font-bold text-[#002C76]">Locally Funded Projects</Text>
        <Text className="mt-1 text-[12px] text-[#475569]">
          Source: {activeBaseUrl}/api/mobile/locally-funded
        </Text>
      </View> */}

      {isLoading ? (
        <View className="flex-1 items-center justify-center px-6">
          <ActivityIndicator size="large" color="#1d4ed8" />
          <Text className="mt-3 text-[13px] text-[#475569]">Loading locally funded projects...</Text>
        </View>
      ) : (
        <View className="flex-1">
          <View className="px-3 pb-1 pt-3">
            <View className="flex-row items-center rounded-2xl border border-[#bfccdf] bg-white px-3 py-2.5">
              <Feather name="search" size={18} color="#64748b" />
              <TextInput
                value={searchQuery}
                onChangeText={setSearchQuery}
                placeholder="Search projects, city, status..."
                placeholderTextColor="#94a3b8"
                className="ml-2 flex-1 text-[14px] text-[#1e293b]"
                autoCapitalize="none"
                autoCorrect={false}
                returnKeyType="search"
              />
              {searchQuery ? (
                <Pressable
                  onPress={() => setSearchQuery("")}
                  className="ml-2 h-6 w-6 items-center justify-center rounded-full bg-[#e2e8f0]"
                  accessibilityRole="button"
                  accessibilityLabel="Clear search"
                >
                  <Feather name="x" size={14} color="#475569" />
                </Pressable>
              ) : null}
            </View>
          </View>

          <FlatList
            contentContainerStyle={{ paddingHorizontal: 12, paddingVertical: 12 }}
            data={filteredProjects}
            keyExtractor={(item, index) => `${item.id}-${index}`}
            renderItem={renderProjectCard}
            refreshControl={
              <RefreshControl
                refreshing={isRefreshing}
                onRefresh={() => {
                  loadProjects(true);
                }}
                tintColor="#1d4ed8"
              />
            }
            ListEmptyComponent={
              <View className="mt-10 rounded-2xl border border-[#dbe3f0] bg-white px-4 py-5">
                <Text className="text-[15px] font-semibold text-[#1e3a8a]">
                  {debouncedSearchQuery.trim() ? "No matching projects" : "No projects available"}
                </Text>
                <Text className="mt-1 text-[12px] leading-[18px] text-[#64748b]">
                  {debouncedSearchQuery.trim()
                    ? "Try another keyword for code, title, city, province, or status."
                    : errorMessage || "No rows were returned by the endpoint."}
                </Text>
                {!debouncedSearchQuery.trim() ? (
                  <Pressable
                    className="mt-4 self-start rounded-xl bg-[#dbeafe] px-4 py-2"
                    onPress={() => {
                      loadProjects(false);
                    }}
                  >
                    <Text className="text-[12px] font-semibold text-[#1e3a8a]">Retry Fetch</Text>
                  </Pressable>
                ) : null}
              </View>
            }
          />
        </View>
      )}
    </SafeAreaView>
  );
}

export const meta = {
  title: "Locally Funded Projects",
};
