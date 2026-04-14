import { Ionicons } from "@expo/vector-icons";
import { useEffect, useRef, useState } from "react";
import {
  Animated,
  LayoutAnimation,
  Platform,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  UIManager,
  View,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";

import { APP_COLORS } from "../../../constants/theme";

const PROJECT_RECORDS = [
  {
    id: "project-1",
    code: "SBDP-2021-14-01-15-001-04",
    title: "Construction of a Concrete School Building",
    location: "Pilar, Abra",
    barangay: "Titik",
    lastUpdatedDate: "2026-03-16",
    lastUpdatedTime: "10:04 AM",
    fundingYear: "2021",
    fundSource: "SBDP",
    procurementType: "admin",
    lgsfAllocation: "P 6,000,000.00",
    obligation: "5,813,687.00",
    utilizationRate: "0.00%",
    physicalStatus: "20.00%",
    physicalProgress: 0.2,
    statusActual: "COMPLETED",
    statusSubaybayan: "NOA ISSUANCE",
  },
  {
    id: "project-2",
    code: "SBDP-2021-14-01-15-001-04",
    title: "Construction of a Concrete School Building",
    location: "Pilar, Abra",
    barangay: "Titik",
    lastUpdatedDate: "2026-03-16",
    lastUpdatedTime: "10:04 AM",
    fundingYear: "2021",
    fundSource: "SBDP",
    procurementType: "admin",
    lgsfAllocation: "P 6,000,000.00",
    obligation: "5,813,687.00",
    utilizationRate: "0.00%",
    physicalStatus: "20.00%",
    physicalProgress: 0.2,
    statusActual: "COMPLETED",
    statusSubaybayan: "NOA ISSUANCE",
  },
  {
    id: "project-3",
    code: "SBDP-2021-14-01-15-001-04",
    title: "Construction of a Concrete School Building",
    location: "Pilar, Abra",
    barangay: "Titik",
    lastUpdatedDate: "2026-03-16",
    lastUpdatedTime: "10:04 AM",
    fundingYear: "2021",
    fundSource: "SBDP",
    procurementType: "admin",
    lgsfAllocation: "P 6,000,000.00",
    obligation: "5,813,687.00",
    utilizationRate: "0.00%",
    physicalStatus: "20.00%",
    physicalProgress: 0.2,
    statusActual: "COMPLETED",
    statusSubaybayan: "NOA ISSUANCE",
  },
];

export default function ProjectsScreen() {
  const [activeGalleryId, setActiveGalleryId] = useState(null);
  const [expandedCardId, setExpandedCardId] = useState(null);
  const [isTransitioning, setIsTransitioning] = useState(false);
  const addSpinValue = useRef(new Animated.Value(0)).current;
  const galleryVisibility = useRef(new Animated.Value(0)).current;

  const addSpinInterpolate = addSpinValue.interpolate({
    inputRange: [0, 1],
    outputRange: ["0deg", "45deg"],
  });

  const galleryTranslateInterpolate = galleryVisibility.interpolate({
    inputRange: [0, 1],
    outputRange: [10, 0],
  });

  useEffect(() => {
    if (
      Platform.OS === "android" &&
      UIManager.setLayoutAnimationEnabledExperimental
    ) {
      UIManager.setLayoutAnimationEnabledExperimental(true);
    }
  }, []);

  const animateOpen = (id) => {
    setActiveGalleryId(id);
    addSpinValue.setValue(0);
    galleryVisibility.setValue(0);

    Animated.parallel([
      Animated.timing(addSpinValue, {
        toValue: 1,
        duration: 220,
        useNativeDriver: true,
      }),
      Animated.timing(galleryVisibility, {
        toValue: 1,
        duration: 220,
        useNativeDriver: true,
      }),
    ]).start(() => {
      setIsTransitioning(false);
    });
  };

  const animateClose = (onComplete) => {
    Animated.parallel([
      Animated.timing(addSpinValue, {
        toValue: 0,
        duration: 180,
        useNativeDriver: true,
      }),
      Animated.timing(galleryVisibility, {
        toValue: 0,
        duration: 180,
        useNativeDriver: true,
      }),
    ]).start(() => {
      setActiveGalleryId(null);
      onComplete();
    });
  };

  const handleOpenGallery = (id) => {
    if (isTransitioning) {
      return;
    }

    setIsTransitioning(true);

    if (activeGalleryId === id) {
      animateClose(() => {
        setIsTransitioning(false);
      });
      return;
    }

    if (activeGalleryId && activeGalleryId !== id) {
      animateClose(() => {
        animateOpen(id);
      });
      return;
    }

    animateOpen(id);
  };

  const handleToggleExpand = (id) => {
    LayoutAnimation.configureNext({
      duration: 240,
      create: {
        type: LayoutAnimation.Types.easeInEaseOut,
        property: LayoutAnimation.Properties.opacity,
      },
      update: {
        type: LayoutAnimation.Types.easeInEaseOut,
      },
      delete: {
        type: LayoutAnimation.Types.easeInEaseOut,
        property: LayoutAnimation.Properties.opacity,
      },
    });

    setExpandedCardId((currentId) => (currentId === id ? null : id));
  };

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <View style={styles.headerContainer}>
        <Text style={styles.headerTitle}>Projects</Text>
      </View>

      <ScrollView
        contentContainerStyle={styles.listContainer}
        showsVerticalScrollIndicator={false}
      >
        {PROJECT_RECORDS.map((record) => (
          <View key={record.id} style={styles.card}>
            {activeGalleryId === record.id ? (
              <Animated.View
                style={[
                  styles.galleryActions,
                  {
                    opacity: galleryVisibility,
                    transform: [
                      { translateX: galleryTranslateInterpolate },
                      { scale: galleryVisibility },
                    ],
                  },
                ]}
              >
                <Pressable style={styles.galleryChip}>
                  <Ionicons name="images-outline" size={12} color="#2e4f95" />
                  <Text style={styles.galleryChipText}>Gallery</Text>
                </Pressable>
              </Animated.View>
            ) : null}

            <View style={styles.cardHeader}>
              <Text style={styles.codeText}>{record.code}</Text>
            </View>

            <View style={styles.actionFloatingContainer}>
              <Pressable
                style={styles.addButton}
                onPress={(event) => {
                  event.stopPropagation();
                  handleOpenGallery(record.id);
                }}
              >
                <Animated.View
                  style={
                    activeGalleryId === record.id
                      ? { transform: [{ rotate: addSpinInterpolate }] }
                      : undefined
                  }
                >
                  <Text style={styles.addButtonText}>+</Text>
                </Animated.View>
              </Pressable>
            </View>

            <Pressable onPress={() => handleToggleExpand(record.id)}>
              <Text style={styles.titleText}>{record.title}</Text>

              {expandedCardId === record.id ? (
                <View style={styles.expandedContentContainer}>
                  <View style={styles.locationHeaderRow}>
                    <View>
                      <Text style={styles.locationLabel}>Pilar, Abra</Text>
                      <Text style={styles.barangayLabel}>Barangay</Text>
                    </View>

                    <View style={styles.lastUpdatedBlock}>
                      <Text style={styles.lastUpdatedLabel}>Last updated</Text>
                      <Text style={styles.lastUpdatedValue}>
                        {record.lastUpdatedDate} {record.lastUpdatedTime}
                      </Text>
                    </View>
                  </View>

                  <Text style={styles.barangayValue}>{record.barangay}</Text>

                  <View style={styles.statsRow}>
                    <View style={styles.halfStatCard}>
                      <Text style={styles.statLabel}>Funding year</Text>
                      <Text style={styles.statValue}>{record.fundingYear}</Text>
                    </View>
                    <View style={styles.halfStatCard}>
                      <Text style={styles.statLabel}>Fund source</Text>
                      <Text style={styles.statValue}>{record.fundSource}</Text>
                    </View>
                  </View>

                  <View style={styles.fullStatCard}>
                    <Text style={styles.statLabel}>Procurement type</Text>
                    <Text style={styles.statValue}>
                      {record.procurementType}
                    </Text>
                  </View>

                  <View style={styles.statsRow}>
                    <View style={styles.halfStatCard}>
                      <Text style={styles.statLabel}>LGSF allocation</Text>
                      <Text style={styles.statValue}>
                        {record.lgsfAllocation}
                      </Text>
                    </View>
                    <View style={styles.halfStatCard}>
                      <Text style={styles.statLabel}>Obligation</Text>
                      <Text style={styles.statValue}>{record.obligation}</Text>
                    </View>
                  </View>

                  <View style={styles.fullStatCard}>
                    <Text style={styles.statLabel}>Utilization rate</Text>
                    <Text style={styles.statValue}>
                      {record.utilizationRate}
                    </Text>
                  </View>

                  <View style={styles.fullStatCard}>
                    <Text style={styles.statLabel}>
                      Physical status (Subaybayan %)
                    </Text>
                    <View style={styles.progressTrack}>
                      <View
                        style={[
                          styles.progressFill,
                          { width: `${record.physicalProgress * 100}%` },
                        ]}
                      />
                    </View>
                    <Text style={styles.statValue}>
                      {record.physicalStatus}
                    </Text>
                  </View>

                  <View style={styles.statsRow}>
                    <View style={styles.halfStatCard}>
                      <Text style={styles.statLabel}>Status (actual)</Text>
                      <View
                        style={[styles.statusPill, styles.statusPillSuccess]}
                      >
                        <Text
                          style={[
                            styles.statusPillText,
                            styles.statusPillSuccessText,
                          ]}
                        >
                          {record.statusActual}
                        </Text>
                      </View>
                    </View>
                    <View style={styles.halfStatCard}>
                      <Text style={styles.statLabel}>Status (subaybayan)</Text>
                      <View style={[styles.statusPill, styles.statusPillInfo]}>
                        <Text
                          style={[
                            styles.statusPillText,
                            styles.statusPillInfoText,
                          ]}
                        >
                          {record.statusSubaybayan}
                        </Text>
                      </View>
                    </View>
                  </View>
                </View>
              ) : (
                <View style={styles.locationContainer}>
                  <Text style={styles.locationText}>{record.location}</Text>
                </View>
              )}
            </Pressable>
          </View>
        ))}
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: {
    flex: 1,
    backgroundColor: APP_COLORS.background,
  },
  headerContainer: {
    paddingHorizontal: 16,
    paddingTop: 10,
    paddingBottom: 8,
  },
  headerTitle: {
    fontSize: 26,
    fontWeight: "600",
    color: APP_COLORS.primary,
    letterSpacing: 0.1,
  },
  listContainer: {
    paddingHorizontal: 12,
    paddingTop: 2,
    paddingBottom: 16,
    gap: 10,
  },
  card: {
    borderRadius: 12,
    borderWidth: 1,
    borderColor: "#d6deeb",
    backgroundColor: "#ffffff",
    paddingTop: 10,
    shadowColor: "#102e72",
    shadowOpacity: 0.04,
    shadowRadius: 8,
    shadowOffset: { width: 0, height: 2 },
    elevation: 1,
  },
  cardHeader: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 12,
  },
  codeText: {
    fontSize: 12,
    fontWeight: "600",
    color: "#2f3c57",
    paddingRight: 94,
    letterSpacing: 0.15,
  },
  actionFloatingContainer: {
    position: "absolute",
    top: 10,
    right: 12,
    zIndex: 2,
  },
  addButton: {
    width: 24,
    height: 24,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: "#d7e1f4",
    backgroundColor: "#f5f8ff",
    alignItems: "center",
    justifyContent: "center",
  },
  addButtonText: {
    marginTop: -0.5,
    fontSize: 14,
    lineHeight: 14,
    fontWeight: "600",
    color: "#3a5ea9",
  },
  galleryActions: {
    position: "absolute",
    top: 11,
    right: 42,
    zIndex: 1,
  },
  galleryChip: {
    height: 22,
    borderRadius: 11,
    borderWidth: 1,
    borderColor: "#2e4f95",
    backgroundColor: "#f6f9ff",
    paddingHorizontal: 7,
    flexDirection: "row",
    alignItems: "center",
  },
  galleryChipText: {
    marginLeft: 4,
    fontSize: 11,
    lineHeight: 11,
    fontWeight: "500",
    color: "#2e4f95",
  },
  titleText: {
    marginTop: 4,
    paddingHorizontal: 12,
    fontSize: 13,
    lineHeight: 18,
    fontWeight: "500",
    color: "#344054",
  },
  locationContainer: {
    marginTop: 10,
    borderTopWidth: 1,
    borderTopColor: "#e4e9f3",
    paddingHorizontal: 12,
    paddingVertical: 9,
  },
  locationText: {
    fontSize: 12,
    lineHeight: 17,
    color: "#667085",
  },
  expandedContentContainer: {
    marginTop: 8,
    borderTopWidth: 1,
    borderTopColor: "#e4e9f3",
    paddingHorizontal: 12,
    paddingTop: 8,
    paddingBottom: 10,
  },
  locationHeaderRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "flex-start",
  },
  locationLabel: {
    fontSize: 11,
    color: "#6b7280",
    fontWeight: "500",
  },
  barangayLabel: {
    marginTop: 2,
    fontSize: 11,
    color: "#6b7280",
    fontWeight: "500",
    letterSpacing: 0.2,
  },
  barangayValue: {
    marginTop: 2,
    fontSize: 12,
    color: "#374151",
    fontWeight: "500",
  },
  lastUpdatedBlock: {
    alignItems: "flex-end",
  },
  lastUpdatedLabel: {
    fontSize: 10,
    fontWeight: "600",
    color: "#6b7280",
    letterSpacing: 0.25,
  },
  lastUpdatedValue: {
    marginTop: 1,
    fontSize: 10,
    color: "#6b7280",
  },
  statsRow: {
    marginTop: 8,
    flexDirection: "row",
    gap: 8,
  },
  halfStatCard: {
    flex: 1,
    borderWidth: 1,
    borderColor: "#d0d5dd",
    borderRadius: 10,
    backgroundColor: "#f3f4f6",
    paddingHorizontal: 8,
    paddingVertical: 7,
  },
  fullStatCard: {
    marginTop: 8,
    borderWidth: 1,
    borderColor: "#d0d5dd",
    borderRadius: 10,
    backgroundColor: "#f3f4f6",
    paddingHorizontal: 8,
    paddingVertical: 7,
  },
  statLabel: {
    fontSize: 10,
    fontWeight: "600",
    color: "#6b7280",
    letterSpacing: 0.2,
  },
  statValue: {
    marginTop: 2,
    fontSize: 12,
    lineHeight: 16,
    fontWeight: "600",
    color: "#344054",
  },
  progressTrack: {
    marginTop: 6,
    height: 10,
    borderRadius: 5,
    backgroundColor: "#aec2ef",
    overflow: "hidden",
  },
  progressFill: {
    height: "100%",
    borderRadius: 5,
    backgroundColor: "#234fc9",
  },
  statusPill: {
    marginTop: 6,
    alignSelf: "flex-start",
    borderRadius: 999,
    paddingHorizontal: 9,
    paddingVertical: 4,
  },
  statusPillSuccess: {
    backgroundColor: "#b9efb7",
  },
  statusPillInfo: {
    backgroundColor: "#b8d7ff",
  },
  statusPillText: {
    fontSize: 9,
    fontWeight: "600",
    letterSpacing: 0.15,
  },
  statusPillSuccessText: {
    color: "#2f8a32",
  },
  statusPillInfoText: {
    color: "#2f5cb0",
  },
});
