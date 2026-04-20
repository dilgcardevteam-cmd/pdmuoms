import { Feather } from "@expo/vector-icons";
import { useLocalSearchParams, useRouter } from "expo-router";
import { useMemo } from "react";
import { Image, Linking, Pressable, ScrollView, Text, View } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { APP_ROUTES } from "../../../../constants/routes";

function parseCoordinate(value) {
  const parsed = Number(value);
  return Number.isFinite(parsed) ? parsed : null;
}

function parseAccuracy(value) {
  const parsed = Number(value);
  return Number.isFinite(parsed) && parsed >= 0 ? parsed : null;
}

function buildGoogleMapsUrl(latitude, longitude) {
  return `https://www.google.com/maps/search/?api=1&query=${latitude},${longitude}`;
}

export default function GalleryImageLocationScreen() {
  const router = useRouter();
  const params = useLocalSearchParams();

  const projectTitle = String(params.projectTitle || "Locally Funded Project");
  const projectCode = String(params.projectCode || "-");
  const stage = String(params.stage || "During");
  const imageUrl = String(params.imageUrl || "");

  const latitude = useMemo(() => parseCoordinate(params.latitude), [params.latitude]);
  const longitude = useMemo(() => parseCoordinate(params.longitude), [params.longitude]);
  const accuracy = useMemo(() => parseAccuracy(params.accuracy), [params.accuracy]);

  const hasCoordinates = latitude !== null && longitude !== null;

  const handleBackToGallery = () => {
    const serializedProject =
      typeof params.project === "string" && params.project.trim()
        ? params.project
        : "";

    if (serializedProject) {
      router.push({
        pathname: APP_ROUTES.projectMonitoring.viewLocallyFundedProject,
        params: {
          project: serializedProject,
          section: "gallery",
        },
      });
      return;
    }

    router.back();
  };

  const handleOpenExternalMap = async () => {
    if (!hasCoordinates) {
      return;
    }

    const mapUrl = buildGoogleMapsUrl(latitude, longitude);
    const canOpen = await Linking.canOpenURL(mapUrl);

    if (canOpen) {
      await Linking.openURL(mapUrl);
    }
  };

  return (
    <SafeAreaView className="flex-1 bg-[#f4f7fc]" edges={["left", "right"]}>
      <ScrollView className="flex-1" contentContainerStyle={{ padding: 16, paddingBottom: 28 }}>
        <View className="flex-row items-center justify-between">
          <Pressable
            accessibilityRole="button"
            accessibilityLabel="Go back"
            onPress={handleBackToGallery}
            className="h-9 w-9 items-center justify-center rounded-full border border-[#c7d6ef] bg-white"
          >
            <Feather name="chevron-left" size={20} color="#0f2f7a" />
          </Pressable>

          <Pressable
            accessibilityRole="button"
            accessibilityLabel="Back to project"
            onPress={() => router.push(APP_ROUTES.projectMonitoring.locallyFundedProjects)}
            className="rounded-full border border-[#c7d6ef] bg-white px-3 py-2"
          >
            <Text className="text-[11px] text-[#0f2f7a]" style={{ fontFamily: "Montserrat-SemiBold" }}>
              Project List
            </Text>
          </Pressable>
        </View>

        <View className="mt-4 overflow-hidden rounded-2xl border border-[#d6e1f4] bg-white">
          {imageUrl ? (
            <Image source={{ uri: imageUrl }} className="h-56 w-full bg-[#dce6f8]" resizeMode="cover" />
          ) : (
            <View className="h-56 items-center justify-center bg-[#edf2fb]">
              <Feather name="image" size={22} color="#7994c7" />
              <Text className="mt-2 text-[12px] text-[#6d83aa]" style={{ fontFamily: "Montserrat" }}>
                Image preview unavailable
              </Text>
            </View>
          )}
        </View>

        <View className="mt-4 rounded-2xl border border-[#d6e1f4] bg-white px-4 py-4">
          <View className="flex-row items-center">
            <Feather name="map-pin" size={16} color="#0f2f7a" />
            <Text className="ml-2 text-[14px] text-[#0f2f7a]" style={{ fontFamily: "Montserrat-SemiBold" }}>
              Captured Coordinates
            </Text>
          </View>

          {hasCoordinates ? (
            <>
              <Text className="mt-3 text-[12px] text-[#344d7c]" style={{ fontFamily: "Montserrat" }}>
                Latitude: {latitude}
              </Text>
              <Text className="mt-1 text-[12px] text-[#344d7c]" style={{ fontFamily: "Montserrat" }}>
                Longitude: {longitude}
              </Text>
              <Text className="mt-1 text-[12px] text-[#344d7c]" style={{ fontFamily: "Montserrat" }}>
                Accuracy: {accuracy !== null ? `${accuracy.toFixed(2)} m` : "Not available"}
              </Text>

              <Pressable
                onPress={handleOpenExternalMap}
                className="mt-4 flex-row items-center justify-center rounded-xl border border-[#0f2f7a] bg-[#0f2f7a] px-4 py-3"
                accessibilityRole="button"
                accessibilityLabel="Open location in Google Maps"
              >
                <Feather name="navigation" size={14} color="#ffffff" />
                <Text className="ml-2 text-[13px] text-white" style={{ fontFamily: "Montserrat-SemiBold" }}>
                  Open in Google Maps
                </Text>
              </Pressable>
            </>
          ) : (
            <View className="mt-3 rounded-xl border border-[#e6d3d3] bg-[#fff5f5] px-3 py-3">
              <Text className="text-[12px] text-[#9f3b3b]" style={{ fontFamily: "Montserrat" }}>
                No location was captured for this image.
              </Text>
            </View>
          )}
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

export const meta = {
  title: "Gallery Image Location",
};
