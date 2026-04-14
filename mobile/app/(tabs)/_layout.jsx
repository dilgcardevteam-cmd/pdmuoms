import { Feather } from "@expo/vector-icons";
import { Tabs } from "expo-router";
import { useColorScheme } from "react-native";

import { TAB_ROUTES } from "../../constants/routes";
import { APP_COLORS } from "../../constants/theme";

export default function TabLayout() {
  const colorScheme = useColorScheme();

  return (
    <Tabs
      screenOptions={{
        tabBarActiveTintColor:
          colorScheme === "dark"
            ? APP_COLORS.tabActiveDark
            : APP_COLORS.tabActiveLight,
        tabBarInactiveTintColor: APP_COLORS.tabInactive,
        headerShown: false,
        tabBarLabelStyle: {
          fontSize: 12,
        },
        tabBarStyle: {
          backgroundColor:
            colorScheme === "dark"
              ? APP_COLORS.tabBackgroundDark
              : APP_COLORS.tabBackgroundLight,
          borderTopColor:
            colorScheme === "dark"
              ? APP_COLORS.tabBorderDark
              : APP_COLORS.tabBorderLight,
          borderTopWidth: 1,
          height: 64,
          paddingBottom: 8,
          paddingTop: 8,
        },
      }}
    >
      {TAB_ROUTES.map((tab) => (
        <Tabs.Screen
          key={tab.route}
          name={tab.route}
          options={{
            title: tab.title,
            tabBarIcon: ({ color, size }) => (
              <Feather name={tab.icon} color={color} size={size} />
            ),
          }}
        />
      ))}
    </Tabs>
  );
}
