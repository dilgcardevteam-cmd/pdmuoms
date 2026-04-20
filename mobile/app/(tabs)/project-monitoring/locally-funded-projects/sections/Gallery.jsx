import { Feather } from "@expo/vector-icons";
import { useMemo, useState } from "react";
import { Pressable, Text, View } from "react-native";

const GALLERY_FILTER_OPTIONS = [
  "All",
  "Before",
  "Project Billboard",
  "Community Billboard",
  "20-40%",
  "50-70%",
  "90%",
  "Completed",
  "During",
];

function FilterDropdown({ value, options, onChange }) {
  const [isOpen, setIsOpen] = useState(false);

  return (
    <View className="z-20">
      <Text className="text-[12px] text-[#6c7ea7]" style={{ fontFamily: "Montserrat-SemiBold" }}>
        Filter by stage
      </Text>

      <View className="relative mt-2">
        <Pressable
          onPress={() => setIsOpen((previous) => !previous)}
          className="flex-row items-center justify-between rounded-xl border border-[#c8d6f1] bg-[#f7faff] px-3 py-2.5"
          accessibilityRole="button"
          accessibilityLabel="Toggle gallery filter dropdown"
        >
          <Text className="text-[13px] text-[#0f2f7a]" style={{ fontFamily: "Montserrat-SemiBold" }}>
            {value}
          </Text>
          <Feather name={isOpen ? "chevron-up" : "chevron-down"} size={16} color="#2c4f96" />
        </Pressable>

        {isOpen ? (
          <View
            className="absolute left-0 right-0 top-full mt-2 rounded-xl border border-[#d5e0f4] bg-white px-2 py-2"
            style={{ zIndex: 50, elevation: 8 }}
          >
            {options.map((option) => {
              const isActive = option === value;
              return (
                <Pressable
                  key={option}
                  onPress={() => {
                    onChange(option);
                    setIsOpen(false);
                  }}
                  className={`mb-1 rounded-lg px-3 py-2 ${isActive ? "bg-[#e8f0ff]" : "bg-transparent"}`}
                  accessibilityRole="button"
                  accessibilityLabel={`Select ${option} filter`}
                >
                  <Text
                    className={`text-[13px] ${isActive ? "text-[#0f2f7a]" : "text-[#4f648f]"}`}
                    style={{ fontFamily: isActive ? "Montserrat-SemiBold" : "Montserrat" }}
                  >
                    {option}
                  </Text>
                </Pressable>
              );
            })}
          </View>
        ) : null}
      </View>
    </View>
  );
}

export default function Gallery() {
  const filterOptions = useMemo(() => GALLERY_FILTER_OPTIONS, []);
  const [selectedFilter, setSelectedFilter] = useState(filterOptions[0]);

  return (
    <View className="mt-3 rounded-2xl border border-[#d7e2f5] bg-white px-4 py-4">
      <Text className="text-[16px] text-[#0f2f7a]" style={{ fontFamily: "Montserrat-SemiBold" }}>
        Gallery
      </Text>

      <View className="mt-3">
        <FilterDropdown value={selectedFilter} options={filterOptions} onChange={setSelectedFilter} />
      </View>

      <View className="mt-4 rounded-xl border border-dashed border-[#c7d8f2] bg-[#f8fbff] px-3 py-3">
        <Text className="text-[12px] text-[#5c719b]" style={{ fontFamily: "Montserrat" }}>
          Gallery list placeholder for filter: {selectedFilter}
        </Text>
      </View>
    </View>
  );
}
