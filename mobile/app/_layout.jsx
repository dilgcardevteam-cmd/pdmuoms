import {
    DarkTheme,
    DefaultTheme,
    ThemeProvider,
} from "@react-navigation/native";
import { Stack } from "expo-router";
import { StatusBar } from "expo-status-bar";
import { useColorScheme } from "react-native";
import "react-native-reanimated";
import "../global.css";

import { APP_COLORS } from "../constants/theme";

export const unstable_settings = {
  anchor: "index",
};

function isLightHexColor(hexColor) {
  const normalized = hexColor.replace("#", "");
  const full =
    normalized.length === 3
      ? normalized
          .split("")
          .map((char) => char + char)
          .join("")
      : normalized;

  const r = parseInt(full.slice(0, 2), 16);
  const g = parseInt(full.slice(2, 4), 16);
  const b = parseInt(full.slice(4, 6), 16);
  const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

  return luminance > 0.6;
}

export default function RootLayout() {
  const colorScheme = useColorScheme();
  const appBackgroundColor = APP_COLORS.background;
  const statusBarStyle = isLightHexColor(appBackgroundColor) ? "dark" : "light";

  return (
    <ThemeProvider value={colorScheme === "dark" ? DarkTheme : DefaultTheme}>
      <Stack>
        <Stack.Screen name="index" options={{ headerShown: false }} />
        <Stack.Screen name="(tabs)" options={{ headerShown: false }} />
      </Stack>
      <StatusBar style={statusBarStyle} backgroundColor={appBackgroundColor} />
    </ThemeProvider>
  );
}
