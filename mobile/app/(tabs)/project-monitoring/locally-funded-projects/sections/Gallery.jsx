import { Feather } from "@expo/vector-icons";
import { useMemo, useState } from "react";
import { Image, Modal, Platform, Pressable, ScrollView, Text, View } from "react-native";
import { Gesture, GestureDetector, GestureHandlerRootView } from "react-native-gesture-handler";
import Animated, { useAnimatedStyle, useSharedValue } from "react-native-reanimated";

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

export default function Gallery({ project }) {
  const filterOptions = useMemo(() => GALLERY_FILTER_OPTIONS, []);
  const [selectedFilter, setSelectedFilter] = useState(filterOptions[0]);
  const [selectedImageUrl, setSelectedImageUrl] = useState("");
  const [selectedImageLabel, setSelectedImageLabel] = useState("");
  const [isViewerOpen, setIsViewerOpen] = useState(false);
  const scale = useSharedValue(1);
  const savedScale = useSharedValue(1);
  const translateX = useSharedValue(0);
  const translateY = useSharedValue(0);
  const savedTranslateX = useSharedValue(0);
  const savedTranslateY = useSharedValue(0);
  const galleryImages = useMemo(
    () => (Array.isArray(project?.galleryImages) ? project.galleryImages : []),
    [project?.galleryImages]
  );

  const pinchGesture = Gesture.Pinch()
    .onUpdate((event) => {
      const nextScale = savedScale.value * event.scale;
      scale.value = Math.max(1, Math.min(nextScale, 4));

      if (scale.value <= 1) {
        translateX.value = 0;
        translateY.value = 0;
        savedTranslateX.value = 0;
        savedTranslateY.value = 0;
      }
    })
    .onEnd(() => {
      savedScale.value = scale.value;
    });

  const panGesture = Gesture.Pan()
    .onUpdate((event) => {
      if (scale.value <= 1) {
        return;
      }

      translateX.value = savedTranslateX.value + event.translationX;
      translateY.value = savedTranslateY.value + event.translationY;
    })
    .onEnd(() => {
      if (scale.value <= 1) {
        translateX.value = 0;
        translateY.value = 0;
        savedTranslateX.value = 0;
        savedTranslateY.value = 0;
        return;
      }

      savedTranslateX.value = translateX.value;
      savedTranslateY.value = translateY.value;
    });

  const zoomPanGesture = Gesture.Simultaneous(pinchGesture, panGesture);

  const animatedImageStyle = useAnimatedStyle(() => ({
    transform: [
      { translateX: translateX.value },
      { translateY: translateY.value },
      { scale: scale.value },
    ],
  }));

  const filteredImages = useMemo(() => {
    if (selectedFilter === "All") {
      return galleryImages;
    }

    return galleryImages.filter((image) => image.category === selectedFilter);
  }, [galleryImages, selectedFilter]);

  const openViewer = (image) => {
    setSelectedImageUrl(String(image?.imageUrl || ""));
    setSelectedImageLabel(String(image?.category || "Image"));
    scale.value = 1;
    savedScale.value = 1;
    translateX.value = 0;
    translateY.value = 0;
    savedTranslateX.value = 0;
    savedTranslateY.value = 0;
    setIsViewerOpen(true);
  };

  const closeViewer = () => {
    setIsViewerOpen(false);
  };

  return (
    <View className="mt-3 rounded-2xl border border-[#d7e2f5] bg-white px-4 py-4">
      <Text className="text-[16px] text-[#0f2f7a]" style={{ fontFamily: "Montserrat-SemiBold" }}>
        Gallery
      </Text>

      <View className="mt-3">
        <FilterDropdown value={selectedFilter} options={filterOptions} onChange={setSelectedFilter} />
      </View>

      <View className="mt-4">
        <View className="flex-row flex-wrap justify-between">
          <Pressable
            className="mb-3 h-[148px] w-[48%] items-center justify-center rounded-xl border border-[#bcd0f0] bg-[#eef4ff]"
            accessibilityRole="button"
            accessibilityLabel="Add image"
          >
            <Feather name="plus-circle" size={24} color="#0f2f7a" />
            <Text className="mt-2 text-[13px] text-[#0f2f7a]" style={{ fontFamily: "Montserrat-SemiBold" }}>
              Add image
            </Text>
          </Pressable>

          {filteredImages.map((image) => (
            <Pressable
              key={String(image.id)}
              onPress={() => openViewer(image)}
              accessibilityRole="button"
              accessibilityLabel="Open image"
              className="mb-3 w-[48%] overflow-hidden rounded-xl border border-[#d3dff3] bg-[#f8fbff]"
            >
              <Image
                source={{ uri: image.imageUrl }}
                className="h-28 w-full bg-[#e2e8f0]"
                resizeMode="cover"
              />
              <View className="px-2 py-2">
                <Text className="text-[11px] text-[#21437f]" style={{ fontFamily: "Montserrat-SemiBold" }}>
                  {image.category || "During"}
                </Text>
              </View>
            </Pressable>
          ))}
        </View>

        {filteredImages.length === 0 ? (
          <View className="rounded-xl border border-dashed border-[#c7d8f2] bg-[#f8fbff] px-3 py-3">
            <Text className="text-[12px] text-[#5c719b]" style={{ fontFamily: "Montserrat" }}>
              No images found for {selectedFilter}.
            </Text>
          </View>
        ) : null}
      </View>

      <Modal
        visible={isViewerOpen}
        transparent
        animationType="fade"
        onRequestClose={closeViewer}
      >
        <GestureHandlerRootView style={{ flex: 1 }}>
          <View className="flex-1 bg-black/90">
            <View className="flex-row items-center justify-between px-4 pb-3 pt-12">
              <Text className="text-[13px] text-white" style={{ fontFamily: "Montserrat-SemiBold" }}>
                {selectedImageLabel}
              </Text>
              <Pressable
                onPress={closeViewer}
                className="h-8 w-8 items-center justify-center rounded-full border border-white/40 bg-white/10"
                accessibilityRole="button"
                accessibilityLabel="Close image viewer"
              >
                <Feather name="x" size={18} color="#ffffff" />
              </Pressable>
            </View>

            {selectedImageUrl ? (
              Platform.OS === "android" ? (
                <View className="flex-1 items-center justify-center">
                  <GestureDetector gesture={zoomPanGesture}>
                    <Animated.View style={{ width: "100%", height: "78%", justifyContent: "center", alignItems: "center" }}>
                      <Animated.Image
                        source={{ uri: selectedImageUrl }}
                        style={[{ width: "100%", height: "100%" }, animatedImageStyle]}
                        resizeMode="contain"
                      />
                    </Animated.View>
                  </GestureDetector>
                </View>
              ) : (
                <ScrollView
                  className="flex-1"
                  contentContainerStyle={{ flexGrow: 1, justifyContent: "center", alignItems: "center" }}
                  minimumZoomScale={1}
                  maximumZoomScale={4}
                  showsHorizontalScrollIndicator={false}
                  showsVerticalScrollIndicator={false}
                  bouncesZoom
                  centerContent
                >
                  <Image
                    source={{ uri: selectedImageUrl }}
                    style={{ width: "100%", height: "78%" }}
                    resizeMode="contain"
                  />
                </ScrollView>
              )
            ) : null}
          </View>
        </GestureHandlerRootView>
      </Modal>
    </View>
  );
}
