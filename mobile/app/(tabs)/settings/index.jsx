import { useRouter } from "expo-router";
import { useState } from "react";
import { Modal, Pressable, StyleSheet, Text, View } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";

import { APP_ROUTES } from "../../../constants/routes";
import { APP_COLORS } from "../../../constants/theme";

export default function SettingsScreen() {
  const router = useRouter();
  const [isLogoutModalVisible, setIsLogoutModalVisible] = useState(false);

  const openLogoutModal = () => {
    setIsLogoutModalVisible(true);
  };

  const closeLogoutModal = () => {
    setIsLogoutModalVisible(false);
  };

  const handleConfirmLogout = () => {
    setIsLogoutModalVisible(false);
    router.replace(APP_ROUTES.login);
  };

  return (
    <SafeAreaView style={styles.container} edges={["top"]}>
      <View style={styles.content}>
        <Text style={styles.title}>Settings</Text>

        <Pressable style={styles.logoutButton} onPress={openLogoutModal}>
          <Text style={styles.logoutButtonText}>Logout</Text>
        </Pressable>
      </View>

      <Modal
        visible={isLogoutModalVisible}
        transparent
        animationType="fade"
        onRequestClose={closeLogoutModal}
      >
        <View style={styles.modalBackdrop}>
          <View style={styles.modalCard}>
            <Text style={styles.modalTitle}>Confirm Logout</Text>
            <Text style={styles.modalMessage}>
              Are you sure you want to logout?
            </Text>

            <View style={styles.modalActions}>
              <Pressable style={styles.cancelButton} onPress={closeLogoutModal}>
                <Text style={styles.cancelButtonText}>Cancel</Text>
              </Pressable>

              <Pressable
                style={styles.confirmButton}
                onPress={handleConfirmLogout}
              >
                <Text style={styles.confirmButtonText}>Logout</Text>
              </Pressable>
            </View>
          </View>
        </View>
      </Modal>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: APP_COLORS.background,
  },
  content: {
    paddingHorizontal: 24,
    paddingTop: 12,
  },
  title: {
    fontSize: 28,
    fontWeight: "700",
    color: APP_COLORS.primary,
    marginBottom: 24,
  },
  logoutButton: {
    height: 50,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: APP_COLORS.accentBorder,
    backgroundColor: APP_COLORS.accentSurface,
    alignItems: "center",
    justifyContent: "center",
  },
  logoutButtonText: {
    fontSize: 18,
    fontWeight: "700",
    color: APP_COLORS.primary,
  },
  modalBackdrop: {
    flex: 1,
    backgroundColor: "rgba(16, 28, 54, 0.4)",
    alignItems: "center",
    justifyContent: "center",
    paddingHorizontal: 24,
  },
  modalCard: {
    width: "100%",
    maxWidth: 360,
    borderRadius: 16,
    borderWidth: 1,
    borderColor: "#d6dfef",
    backgroundColor: APP_COLORS.backgroundCard,
    paddingHorizontal: 18,
    paddingVertical: 18,
  },
  modalTitle: {
    fontSize: 20,
    fontWeight: "700",
    color: APP_COLORS.primary,
  },
  modalMessage: {
    marginTop: 10,
    fontSize: 15,
    color: APP_COLORS.primaryMuted,
    lineHeight: 20,
  },
  modalActions: {
    marginTop: 20,
    flexDirection: "row",
    justifyContent: "flex-end",
  },
  cancelButton: {
    minWidth: 90,
    height: 42,
    borderRadius: 10,
    borderWidth: 1,
    borderColor: "#c4d0e6",
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: "#f8fbff",
  },
  cancelButtonText: {
    fontSize: 15,
    fontWeight: "600",
    color: APP_COLORS.primaryMuted,
  },
  confirmButton: {
    marginLeft: 10,
    minWidth: 90,
    height: 42,
    borderRadius: 10,
    borderWidth: 1,
    borderColor: APP_COLORS.accentBorder,
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: APP_COLORS.accentSurface,
  },
  confirmButtonText: {
    fontSize: 15,
    fontWeight: "700",
    color: APP_COLORS.primary,
  },
});
