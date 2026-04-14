import { Feather } from "@expo/vector-icons";
import { Tabs } from "expo-router";
import { useRef, useState } from "react";
import {
  Animated,
  Easing,
  Image,
  Pressable,
  Text,
  View,
} from "react-native";
import { useSafeAreaInsets } from "react-native-safe-area-context";

import { TAB_ROUTES } from "../../constants/routes";
import { APP_COLORS } from "../../constants/theme";

export default function TabLayout() {
  const insets = useSafeAreaInsets();
  const [isDrawerVisible, setIsDrawerVisible] = useState(false);
  const drawerProgress = useRef(new Animated.Value(0)).current;
  const tabIndicatorIndex = useRef(new Animated.Value(0)).current;
  const previousTabIndex = useRef(0);
  const [tabTrackWidth, setTabTrackWidth] = useState(0);

  const drawerMenuGroups = [
    [
      { label: "Project Monitoring", icon: "trello" },
      { label: "Rapid Subproject Sustainability Assessment", icon: "list" },
      { label: "LGU Reportorial Requirements", icon: "file-text" },
      { label: "Pre-Implementation Documents", icon: "folder" },
      { label: "Ticketing System", icon: "message-square" },
    ],
    [
      { label: "Data Management", icon: "database" },
      { label: "User Management", icon: "users" },
      { label: "Utilities", icon: "tool" },
    ],
  ];

  const activeTabColor = APP_COLORS.primary;
  const inactiveTabColor = APP_COLORS.primaryMuted;
  const drawerWidth = 320;
  const headerStyle = {
    backgroundColor: APP_COLORS.tabBackgroundLight,
    borderBottomColor: APP_COLORS.tabBorderLight,
    borderBottomWidth: 1,
    elevation: 0,
    shadowOpacity: 0,
  };
  const headerTitleStyle = {
    color: APP_COLORS.primary,
    fontSize: 18,
    fontWeight: "500",
    marginLeft: -8,
  };
  const drawerPanelStyle = {
    width: 320,
    maxWidth: "86%",
    backgroundColor: APP_COLORS.primary,
    shadowColor: "#0f172a",
    shadowOpacity: 0.24,
    shadowRadius: 14,
    shadowOffset: { width: 6, height: 0 },
    elevation: 18,
  };

  const openDrawer = () => {
    if (isDrawerVisible) {
      return;
    }

    setIsDrawerVisible(true);
    Animated.timing(drawerProgress, {
      toValue: 1,
      duration: 260,
      easing: Easing.out(Easing.cubic),
      useNativeDriver: true,
    }).start();
  };

  const closeDrawer = () => {
    Animated.timing(drawerProgress, {
      toValue: 0,
      duration: 220,
      easing: Easing.in(Easing.cubic),
      useNativeDriver: true,
    }).start(({ finished }) => {
      if (finished) {
        setIsDrawerVisible(false);
      }
    });
  };

  const renderTabBar = ({ state, descriptors, navigation }) => {
    const activeIndex = state.index;
    const indicatorWidth =
      state.routes.length > 0 ? tabTrackWidth / state.routes.length : 0;

    if (previousTabIndex.current !== activeIndex) {
      Animated.timing(tabIndicatorIndex, {
        toValue: activeIndex,
        duration: 220,
        easing: Easing.out(Easing.cubic),
        useNativeDriver: true,
      }).start();
      previousTabIndex.current = activeIndex;
    }

    return (
      <View
        className="border-t bg-white"
        style={[
          { borderTopColor: APP_COLORS.tabBorderLight },
          { paddingBottom: Math.max(insets.bottom, 10) },
        ]}
      >
        <View
          className="relative h-1 border-b"
          style={{
            backgroundColor: APP_COLORS.accentSurface,
            borderBottomColor: APP_COLORS.tabBorderLight,
          }}
          onLayout={(event) => {
            setTabTrackWidth(event.nativeEvent.layout.width);
          }}
        >
          <Animated.View
            style={[
              {
                position: "absolute",
                top: 0,
                bottom: 0,
                backgroundColor: APP_COLORS.primary,
                width: indicatorWidth,
                transform: [
                  {
                    translateX: Animated.multiply(
                      tabIndicatorIndex,
                      indicatorWidth
                    ),
                  },
                ],
              },
            ]}
          />
        </View>

        <View className="flex-row items-center justify-around pt-2.5 pb-3">
          {state.routes.map((route, index) => {
            const { options } = descriptors[route.key];
            const isFocused = state.index === index;
            const label = options.title ?? route.name;
            const iconName = TAB_ROUTES[index]?.icon;
            const tintColor = isFocused ? activeTabColor : inactiveTabColor;

            const handlePress = () => {
              const event = navigation.emit({
                type: "tabPress",
                target: route.key,
                canPreventDefault: true,
              });

              if (!isFocused && !event.defaultPrevented) {
                navigation.navigate(route.name);
              }
            };

            return (
              <Pressable
                key={route.key}
                accessibilityRole="button"
                accessibilityState={isFocused ? { selected: true } : {}}
                accessibilityLabel={options.tabBarAccessibilityLabel}
                testID={options.tabBarButtonTestID}
                onPress={handlePress}
                className="min-h-[64px] items-center justify-center px-1.5"
                style={({ pressed }) => ({ opacity: pressed ? 0.72 : 1 })}
              >
                <View className="w-full items-center justify-center gap-1.5">
                  <View className="h-6 w-6 items-center justify-center">
                    <Feather name={iconName} size={22} color={tintColor} />
                  </View>
                  <Text
                    numberOfLines={1}
                    className="text-center text-[9px] leading-[11px]"
                    style={[
                      { includeFontPadding: false },
                      { color: tintColor },
                      isFocused && { fontWeight: "500" },
                    ]}
                  >
                    {label}
                  </Text>
                </View>
              </Pressable>
            );
          })}
        </View>
      </View>
    );
  };

  return (
    <View className="flex-1">
      <Tabs
        screenOptions={{
          headerShown: true,
          tabBarHideOnKeyboard: true,
          headerTitleAlign: "left",
          headerStyle,
          headerTitleStyle,
          headerLeft: () => (
            <Pressable
              onPress={openDrawer}
              className="ml-[14px] mr-4 p-1"
              style={({ pressed }) => ({ opacity: pressed ? 0.6 : 1 })}
              hitSlop={8}
              accessibilityRole="button"
              accessibilityLabel="Open menu"
            >
              <Feather name="menu" size={24} color={APP_COLORS.primary} />
            </Pressable>
          ),
        }}
        tabBar={renderTabBar}
      >
        {TAB_ROUTES.map((tab) => (
          <Tabs.Screen
            key={tab.route}
            name={tab.route}
            options={{
              title: tab.title,
            }}
          />
        ))}
      </Tabs>

      {isDrawerVisible ? (
        <View className="absolute inset-0 z-40 flex-row" pointerEvents="box-none">
          <Pressable
            className="absolute inset-0"
            style={{ backgroundColor: "rgba(15, 23, 42, 0.28)" }}
            onPress={closeDrawer}
          />

          <Animated.View
            className="rounded-r-[14px] px-[22px] pt-14"
            style={[
              drawerPanelStyle,
              {
                transform: [
                  {
                    translateX: drawerProgress.interpolate({
                      inputRange: [0, 1],
                      outputRange: [-drawerWidth, 0],
                    }),
                  },
                ],
              },
            ]}
          >
            <View className="flex-row items-center">
              <Image
                source={require("../../assets/images/dilg-logo.png")}
                className="h-9 w-9"
                resizeMode="contain"
              />
              <Text className="ml-2.5 text-[38px] font-bold tracking-[0.4px] text-white">
                PRISM
              </Text>
            </View>
            <View
              className="mt-[18px] border-b"
              style={{ borderBottomColor: "rgba(255, 255, 255, 0.5)" }}
            />

            <View className="mt-4">
              {drawerMenuGroups.map((group, groupIndex) => (
                <View key={`drawer-group-${groupIndex}`}>
                  {group.map((item) => (
                    <Pressable
                      key={item.label}
                      className="mb-2 flex-row items-center rounded-xl px-2 py-2.5"
                      style={({ pressed }) => ({
                        backgroundColor: pressed
                          ? "rgba(255, 255, 255, 0.12)"
                          : "transparent",
                        opacity: pressed ? 0.9 : 1,
                      })}
                      onPress={() => {}}
                    >
                      <Feather
                        name={item.icon}
                        size={16}
                        color="#EAF1FF"
                      />
                      <Text className="ml-3 flex-1 text-[13px] leading-[18px] text-white/90">
                        {item.label}
                      </Text>
                    </Pressable>
                  ))}

                </View>
              ))}
            </View>
          </Animated.View>
        </View>
      ) : null}
    </View>
  );
}
