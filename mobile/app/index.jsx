import { useRouter } from "expo-router";
import { useState } from "react";
import {
    Image,
    KeyboardAvoidingView,
    Platform,
    Pressable,
    Text,
    TextInput,
    View,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";

import { APP_ROUTES } from "../constants/routes";

export default function LoginScreen() {
  const router = useRouter();
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");

  const handleLoginPress = () => {
    router.replace(APP_ROUTES.homeTab);
  };

  return (
    <SafeAreaView className="flex-1 bg-[#f7f9fc]">
      <KeyboardAvoidingView
        className="flex-1"
        behavior={Platform.OS === "ios" ? "padding" : undefined}
      >
        <View className="flex-1 justify-center px-6">
          <View className="mb-9 items-center">
            <View className="mb-3 h-[94px] w-[94px] items-center justify-center rounded-full border border-[#d9e2f2] bg-white shadow-md shadow-[#10337e]/10">
              <Image
                source={require("../assets/images/dilg-logo.png")}
                className="h-[78px] w-[78px]"
                resizeMode="contain"
              />
            </View>
            <Text className="mb-1.5 text-[38px] font-extrabold tracking-[0.4px] text-[#10337e]">
              PDMUOMS
            </Text>
            <Text className="text-center text-[17px] leading-[22px] text-[#27477d]">
              PDMU Operations Management System
            </Text>
          </View>

          <View className="rounded-[18px] border border-[#d6dfef] bg-[#fdfefe] px-4 py-[18px]">
            <View className="mb-[18px]">
              <Text className="mb-1.5 text-[13px] font-bold tracking-[0.4px] text-[#10337e]">
                USERNAME
              </Text>
              <TextInput
                value={username}
                onChangeText={setUsername}
                placeholder="Enter username"
                autoCapitalize="none"
                placeholderTextColor="#97a4bc"
                className="h-[50px] rounded-xl border border-[#3f68be] bg-white px-[14px] text-[#10337e]"
              />

              <Text className="mb-1.5 mt-4 text-[13px] font-bold tracking-[0.4px] text-[#10337e]">
                PASSWORD
              </Text>
              <TextInput
                value={password}
                onChangeText={setPassword}
                placeholder="Enter password"
                secureTextEntry
                placeholderTextColor="#97a4bc"
                className="h-[50px] rounded-xl border border-[#3f68be] bg-white px-[14px] text-[#10337e]"
              />
            </View>

            <Pressable
              className="h-[50px] w-full items-center justify-center self-center rounded-xl border border-[#3f68be] bg-[#d8e4f8]"
              onPress={handleLoginPress}
            >
              <Text className="text-[20px] font-bold text-[#10337e]">
                Login
              </Text>
            </Pressable>
          </View>
        </View>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}
