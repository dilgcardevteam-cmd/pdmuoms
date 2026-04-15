import { Feather } from "@expo/vector-icons";
import { Tabs, usePathname, useRouter } from "expo-router";
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
import { useAuth } from "../../contexts/AuthContext";
import { useFetchLoggedUser } from "../../hooks/useFetchLoggedUser";

import {
  APP_ROUTES,
  PROJECT_MONITORING_ROUTES,
  TAB_ROUTES,
} from "../../constants/routes";
import { APP_COLORS } from "../../constants/theme";

const PROJECT_MONITORING_KEY = "project-monitoring";
const PROJECT_MONITORING_SUBMENU_HEIGHT = 208;
const VISIBLE_TAB_ROUTE_NAMES = TAB_ROUTES.map((tab) => tab.route);

const DRAWER_MENU_ITEMS = [
  {
    key: "home",
    label: "Home",
    icon: "grid",
    route: APP_ROUTES.homeTab,
  },
  {
    key: "messages",
    label: "Messages",
    icon: "message-square",
    route: APP_ROUTES.message,
  },
  {
    key: "project-monitoring",
    label: "Project Monitoring",
    icon: "trello",
    children: [
      {
        key: "locally-funded-projects",
        label: "Locally Funded Projects",
        icon: "briefcase",
        route: APP_ROUTES.projectMonitoring.locallyFundedProjects,
      },
      {
        key: "rlip-lime-20-development-fund",
        label: "RLIP/LIME-20% Development Fund",
        icon: "feather",
        route: APP_ROUTES.projectMonitoring.rlipLimeDevelopmentFund,
      },
      {
        key: "project-at-risk",
        label: "Project At Risk",
        icon: "alert-triangle",
        route: APP_ROUTES.projectMonitoring.projectAtRisk,
      },
      {
        key: "sglgif-portal",
        label: "SGLGIF Portal",
        icon: "award",
        route: APP_ROUTES.projectMonitoring.sglgifPortal,
      },
    ],
  },
  {
    key: "rapid-subproject-sustainability-assessment",
    label: "Rapid Subproject Sustainability Assessment",
    icon: "list",
  },
  {
    key: "lgu-reportorial-requirements",
    label: "LGU Reportorial Requirements",
    icon: "file-text",
  },
  {
    key: "pre-implementation-documents",
    label: "Pre-Implementation Documents",
    icon: "folder",
  },
  {
    key: "ticketing-system",
    label: "Ticketing System",
    icon: "message-square",
  },
  {
    key: "data-management",
    label: "Data Management",
    icon: "database",
  },
  {
    key: "user-management",
    label: "User Management",
    icon: "users",
  },
  {
    key: "utilities",
    label: "Utilities",
    icon: "tool",
  },
  
    // (settings moved to bottom area)
];

export default function TabLayout() {
  const router = useRouter();
  const pathname = usePathname();
  const insets = useSafeAreaInsets();
  const { firstName, lastName } = useFetchLoggedUser();
  const { signOut } = useAuth();
  const [isDrawerVisible, setIsDrawerVisible] = useState(false);
  const [expandedMenus, setExpandedMenus] = useState({
    "project-monitoring": false,
  });
  const drawerProgress = useRef(new Animated.Value(0)).current;
  const projectMonitoringAnimation = useRef(new Animated.Value(0)).current;
  const tabIndicatorIndex = useRef(new Animated.Value(0)).current;
  const previousTabIndex = useRef(0);
  // settings moved to bottom area
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
    fontFamily: "Montserrat-SemiBold",
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

  const toggleMenuSection = (sectionKey) => {
    const willExpand = !expandedMenus[sectionKey];

    if (sectionKey === PROJECT_MONITORING_KEY) {
      Animated.timing(projectMonitoringAnimation, {
        toValue: willExpand ? 1 : 0,
        duration: 220,
        easing: Easing.out(Easing.cubic),
        useNativeDriver: false,
      }).start();
    }

    setExpandedMenus((currentState) => ({
      ...currentState,
      [sectionKey]: !currentState[sectionKey],
    }));
  };

  const handleDrawerItemPress = (routePath) => {
    if (!routePath) {
      return;
    }

    router.push(routePath);
    closeDrawer();
  };

  const shouldHideBottomNavbar = pathname.includes("/project-monitoring/");

  const renderTabBar = ({ state, descriptors, navigation }) => {
    // Always hide the bottom tab bar (we're moving messages/settings into drawer)
    return null;

    const visibleRoutes = state.routes.filter((route) =>
      VISIBLE_TAB_ROUTE_NAMES.includes(route.name)
    );
    const activeRoute = state.routes[state.index];
    const activeVisibleIndex = visibleRoutes.findIndex(
      (route) => route.key === activeRoute?.key
    );
    const indicatorWidth =
      visibleRoutes.length > 0 ? tabTrackWidth / visibleRoutes.length : 0;

    if (previousTabIndex.current !== activeVisibleIndex) {
      Animated.timing(tabIndicatorIndex, {
        toValue: activeVisibleIndex < 0 ? 0 : activeVisibleIndex,
        duration: 220,
        easing: Easing.out(Easing.cubic),
        useNativeDriver: true,
      }).start();
      previousTabIndex.current = activeVisibleIndex;
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
          {visibleRoutes.map((route, visibleIndex) => {
            const { options } = descriptors[route.key];
            const isFocused = activeRoute?.key === route.key;
            const label = options.title ?? route.name;
            const iconName = TAB_ROUTES[visibleIndex]?.icon;
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
          headerRight: () => (
            <Pressable
              onPress={() => router.push(APP_ROUTES.notifications)}
              className="mr-[14px] p-1"
              style={({ pressed }) => ({ opacity: pressed ? 0.6 : 1 })}
              hitSlop={8}
              accessibilityRole="button"
              accessibilityLabel="Open notifications"
            >
              <Feather name="bell" size={22} color={APP_COLORS.primary} />
            </Pressable>
          ),
        }}
        tabBar={renderTabBar}
      >
        {TAB_ROUTES.map((tab) => (
          <Tabs.Screen
            style={{fontFamily: "Montserrat-Regular"}}
            key={tab.route}
            name={tab.route}
            options={{
              title: tab.title,
            }}
          />
        ))}

        {PROJECT_MONITORING_ROUTES.map((projectRoute) => (
          <Tabs.Screen
            key={projectRoute.route}
            name={projectRoute.route}
            options={{
              title: projectRoute.title,
              href: null,
            }}
          />
        ))}

        {/* Explicitly register screens that used to be tabs so we can set friendly titles */}
        <Tabs.Screen
          name="message/index"
          options={{ title: "Messages" }}
        />

        <Tabs.Screen
          name="notifications/index"
          options={{ title: "Notifications" }}
        />

        <Tabs.Screen
          name="settings/index"
          options={{ title: "Settings" }}
        />
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

            {/* Profile card */}
            <View className="mt-4 flex-1">
              <View
                className="rounded-xl px-3 py-3"
                style={{ backgroundColor: 'rgba(255,255,255,0.08)' }}
              >
                <View className="flex-row items-center">
                  <View className="h-10 w-10 rounded-full items-center justify-center bg-white/10">
                    <Feather name="user" size={20} color="#EAF1FF" />
                  </View>
                  <View className="ml-3">
                    <Text className="text-[16px] font-semibold text-white">{(firstName ?? 'User') + (lastName ? ' ' + lastName : '')}</Text>
                  </View>
                </View>
              </View>
            </View>

            <View
              className="mt-4 border-b"
              style={{ borderBottomColor: "rgba(255, 255, 255, 0.12)" }}
            />

            <View className="mt-4">
              {DRAWER_MENU_ITEMS.map((item, idx) => {
                const hasChildren = Array.isArray(item.children) && item.children.length > 0;
                const isExpanded = expandedMenus[item.key];
                const isProjectMonitoringSection = item.key === PROJECT_MONITORING_KEY;
                const submenuHeight = projectMonitoringAnimation.interpolate({
                  inputRange: [0, 1],
                  outputRange: [0, PROJECT_MONITORING_SUBMENU_HEIGHT],
                });
                const submenuTranslateY = projectMonitoringAnimation.interpolate({
                  inputRange: [0, 1],
                  outputRange: [-8, 0],
                });
                const chevronRotate = projectMonitoringAnimation.interpolate({
                  inputRange: [0, 1],
                  outputRange: ["0deg", "180deg"],
                });

                return (
                  <View key={item.key ?? item.label ?? idx}>
                    <Pressable
                      className="mb-2 flex-row items-center rounded-xl px-2 py-2.5"
                      style={({ pressed }) => ({
                        backgroundColor: pressed
                          ? "rgba(255, 255, 255, 0.12)"
                          : "transparent",
                        opacity: pressed ? 0.9 : 1,
                      })}
                      accessibilityRole="button"
                      accessibilityState={hasChildren ? { expanded: !!isExpanded } : undefined}
                      onPress={() => {
                        if (hasChildren) {
                          toggleMenuSection(item.key);
                          return;
                        }

                        handleDrawerItemPress(item.route);
                      }}
                    >
                      <Feather
                        name={item.icon}
                        size={16}
                        color="#EAF1FF"
                      />
                      <Text className="ml-3 flex-1 text-[13px] leading-[18px] text-white/90">
                        {item.label}
                      </Text>
                      {hasChildren ? (
                        isProjectMonitoringSection ? (
                          <Animated.View style={{ transform: [{ rotate: chevronRotate }] }}>
                            <Feather
                              name="chevron-down"
                              size={16}
                              color="#C4D7FF"
                            />
                          </Animated.View>
                        ) : (
                          <Feather
                            name={isExpanded ? "chevron-up" : "chevron-down"}
                            size={16}
                            color="#C4D7FF"
                          />
                        )
                      ) : null}
                    </Pressable>

                    {hasChildren && isProjectMonitoringSection ? (
                      <Animated.View
                        className="overflow-hidden"
                        pointerEvents={isExpanded ? "auto" : "none"}
                        style={{
                          height: submenuHeight,
                          opacity: projectMonitoringAnimation,
                          transform: [{ translateY: submenuTranslateY }],
                        }}
                      >
                        <View className="mb-2 ml-3 rounded-xl border border-white/20 bg-white/10 px-2 py-2">
                          {item.children.map((childItem, cidx) => (
                            <Pressable
                              key={childItem.key ?? childItem.label ?? cidx}
                              className="mb-1.5 flex-row items-center rounded-lg px-2 py-2"
                              style={({ pressed }) => ({
                                backgroundColor: pressed
                                  ? "rgba(255, 255, 255, 0.16)"
                                  : "transparent",
                                opacity: pressed ? 0.92 : 1,
                              })}
                              accessibilityRole="button"
                              onPress={() => {
                                handleDrawerItemPress(childItem.route);
                              }}
                            >
                              <Feather
                                name={childItem.icon}
                                size={14}
                                color="#EAF1FF"
                              />
                              <Text className="ml-3 flex-1 text-[13px] leading-[18px] text-white">
                                {childItem.label}
                              </Text>
                            </Pressable>
                          ))}
                        </View>
                      </Animated.View>
                    ) : null}

                    {hasChildren && !isProjectMonitoringSection && isExpanded ? (
                      <View className="mb-2 ml-3 rounded-xl border border-white/20 bg-white/10 px-2 py-2">
                        {item.children.map((childItem, cidx) => (
                          <Pressable
                            key={childItem.key ?? childItem.label ?? cidx}
                            className="mb-1.5 flex-row items-center rounded-lg px-2 py-2"
                            style={({ pressed }) => ({
                              backgroundColor: pressed
                                ? "rgba(255, 255, 255, 0.16)"
                                : "transparent",
                              opacity: pressed ? 0.92 : 1,
                            })}
                            accessibilityRole="button"
                            onPress={() => {
                              handleDrawerItemPress(childItem.route);
                            }}
                          >
                            <Feather
                              name={childItem.icon}
                              size={14}
                              color="#EAF1FF"
                            />
                            <Text className="ml-3 flex-1 text-[13px] leading-[18px] text-white">
                              {childItem.label}
                            </Text>
                          </Pressable>
                        ))}
                      </View>
                    ) : null}
                  </View>
                );
              })}
            </View>

            <View className="mt-auto pb-6 pt-4">
              <View
                className="mb-4 border-t"
                style={{ borderTopColor: "rgba(255,255,255,0.12)" }}
              />

              <Pressable
                className="flex-row items-center rounded-xl px-2 py-3"
                style={({ pressed }) => ({
                  backgroundColor: pressed
                    ? "rgba(255, 255, 255, 0.08)"
                    : "transparent",
                  opacity: pressed ? 0.92 : 1,
                })}
                accessibilityRole="button"
                onPress={() => {
                  handleDrawerItemPress(APP_ROUTES.settings);
                }}
              >
                <Feather name="settings" size={16} color="#EAF1FF" />
                <Text className="ml-3 text-[14px] font-semibold text-white">
                  Settings
                </Text>
              </Pressable>

              <Pressable
                className="mt-2 flex-row items-center rounded-xl px-2 py-3"
                style={({ pressed }) => ({
                  backgroundColor: pressed
                    ? "rgba(248, 113, 113, 0.12)"
                    : "transparent",
                  opacity: pressed ? 0.92 : 1,
                })}
                accessibilityRole="button"
                onPress={async () => {
                  try {
                    await signOut();
                  } catch (e) {
                    // ignore
                  }
                  router.replace(APP_ROUTES.login);
                }}
              >
                <Feather name="log-out" size={16} color={APP_COLORS.primaryRed} />
                <Text className="ml-3 text-[14px] font-semibold text-[#FCA5A5]">
                  Log out
                </Text>
              </Pressable>
            </View>
          </Animated.View>
        </View>
      ) : null}
    </View>
  );
}
