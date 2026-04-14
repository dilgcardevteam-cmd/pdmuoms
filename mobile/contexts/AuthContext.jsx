import * as SecureStore from "expo-secure-store";
import { createContext, useContext, useEffect, useMemo, useState } from "react";

const AUTH_STORAGE_KEY = "pdmuoms.mobile.auth.session";

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [session, setSession] = useState(null);
  const [isHydrating, setIsHydrating] = useState(true);

  useEffect(() => {
    let isMounted = true;

    const hydrate = async () => {
      try {
        const rawSession = await SecureStore.getItemAsync(AUTH_STORAGE_KEY);

        if (!isMounted) {
          return;
        }

        if (!rawSession) {
          setSession(null);
          return;
        }

        const parsedSession = JSON.parse(rawSession);
        setSession(parsedSession);
      } catch {
        if (isMounted) {
          setSession(null);
        }
      } finally {
        if (isMounted) {
          setIsHydrating(false);
        }
      }
    };

    hydrate();

    return () => {
      isMounted = false;
    };
  }, []);

  const signIn = async (payload) => {
    const nextSession = {
      username: payload?.username ?? "",
      loggedInAt: Date.now(),
    };

    await SecureStore.setItemAsync(
      AUTH_STORAGE_KEY,
      JSON.stringify(nextSession)
    );
    setSession(nextSession);
  };

  const signOut = async () => {
    await SecureStore.deleteItemAsync(AUTH_STORAGE_KEY);
    setSession(null);
  };

  const value = useMemo(
    () => ({
      session,
      isHydrating,
      isAuthenticated: Boolean(session),
      signIn,
      signOut,
    }),
    [session, isHydrating]
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
  const context = useContext(AuthContext);

  if (!context) {
    throw new Error("useAuth must be used within AuthProvider");
  }

  return context;
}
