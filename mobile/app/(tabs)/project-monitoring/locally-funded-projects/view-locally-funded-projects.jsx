import { Feather } from "@expo/vector-icons";
import { useLocalSearchParams, useRouter } from "expo-router";
import { useMemo } from "react";
import { Pressable, Text, View } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";

function parseProjectParam(rawValue) {
  if (typeof rawValue !== "string" || !rawValue.trim()) {
    return null;
  }

  try {
    return JSON.parse(rawValue);
  } catch (_error) {
    return null;
  }
}

function DetailRow({ label, value }) {
  return (
    <View className="mt-2 flex-row items-center">
      <Feather name={label === "Project Code" ? "paperclip" : "calendar"} size={20} color="#0f2f7a" />
      <Text
        className="ml-2 text-[14px] text-[#2f4f9c]"
        style={{ fontFamily: "Montserrat" }}
      >
        {value || "N/A"}
      </Text>
    </View>
  );
}

export default function ViewLocallyFundedProjectsScreen() {
  const router = useRouter();
  const params = useLocalSearchParams();

  const project = useMemo(() => parseProjectParam(params.project), [params.project]);

  const projectTitle = String(project?.title ?? "Unknown Project");
  const projectCode = String(project?.code ?? "N/A");
  const fundingYear = String(project?.fundingYear ?? "N/A");
  const fundSource = String(project?.fundSource ?? "N/A");

  return (
    <SafeAreaView className="flex-1 bg-[#f1eff5]" edges={["left", "right"]}>
      <View className="px-4 pt-4">
        <View className="flex-row items-start">
          <Pressable
            accessibilityRole="button"
            accessibilityLabel="Go back"
            onPress={() => router.back()}
            className="mr-2 mt-0.5 h-7 w-7 items-center justify-center rounded-full"
            hitSlop={8}
          >
            <Feather name="chevron-left" size={24} color="#0f2f7a" />
          </Pressable>

          <Text
            className="flex-1 text-[16px] leading-[22px] text-[#0f2f7a]"
            style={{ fontFamily: "Montserrat-SemiBold" }}
          >
            {projectTitle}
          </Text>
        </View>

        <View className="mt-3 border-b border-[#b8bdc9]" />

        <View className="mt-3">
          <Text
            className="text-[16px] text-[#0f2f7a]"
            style={{ fontFamily: "Montserrat-SemiBold" }}
          >
            Project Information
          </Text>

          <DetailRow label="Project Code" value={projectCode} />
          <DetailRow label="Funding Year" value={fundingYear} />
          <DetailRow label="Funding Source" value={fundSource} />
        </View>
      </View>
    </SafeAreaView>
  );
}

export const meta = {
  title: "View Locally Funded Project",
};
