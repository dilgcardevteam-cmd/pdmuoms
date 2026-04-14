import { Feather } from "@expo/vector-icons";
import { useCallback, useEffect, useState } from "react";
import {
  ActivityIndicator,
  FlatList,
  Pressable,
  RefreshControl,
  Text,
  View,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { API_URL } from "../../../constants/api";
import { useWebAppRequest } from "../../../hooks/useWebAppRequest";
import { APP_COLORS } from "../../../constants/theme";

const pesoFormatter = new Intl.NumberFormat("en-PH", {
  style: "currency",
  currency: "PHP",
  maximumFractionDigits: 2,
});

const statusPaletteByLabel = {
  completed: { backgroundColor: APP_COLORS.successLight, color: APP_COLORS.success },
  ongoing: { backgroundColor: APP_COLORS.primaryBlueLight, color: APP_COLORS.primaryBlue },
  pending: { backgroundColor: APP_COLORS.statusPendingLight, color: APP_COLORS.statusPending },
  "noa issuance": { backgroundColor: APP_COLORS.statusInfoLight, color: APP_COLORS.statusInfo },
  delayed: { backgroundColor: APP_COLORS.statusDelayedLight, color: APP_COLORS.statusDelayed },
  neutral: { backgroundColor: APP_COLORS.statusNeutralLight, color: APP_COLORS.statusNeutral },
};

function formatMoney(value) {
  if (value === null || value === undefined || Number.isNaN(Number(value))) {
    return "-";
  }

  return pesoFormatter.format(Number(value));
}

function formatPercent(value) {
  if (value === null || value === undefined || Number.isNaN(Number(value))) {
    return "0.00%";
  }

  return `${Number(value).toFixed(2)}%`;
}

function formatUpdatedAt(value) {
  if (!value) {
    return "-";
  }

  const parsed = new Date(value);
  if (Number.isNaN(parsed.getTime())) {
    return "-";
  }

  const month = parsed.toLocaleString("en-US", { month: "short" });
  const day = String(parsed.getDate()).padStart(2, "0");
  const year = parsed.getFullYear();
  const time = parsed
    .toLocaleString("en-US", {
      hour: "2-digit",
      minute: "2-digit",
      hour12: true,
    })
    .replace(/\s/g, "");

  return `${month} ${day}, ${year} ${time}`;
}

function getStatusPalette(statusValue) {
  const normalized = String(statusValue || "").trim().toLowerCase();
  if (!normalized) {
    return statusPaletteByLabel.neutral;
  }

  const directMatch = statusPaletteByLabel[normalized];
  if (directMatch) {
    return directMatch;
  }

  if (normalized.includes("complete")) {
    return statusPaletteByLabel.completed;
  }

  if (normalized.includes("ongoing") || normalized.includes("progress")) {
    return statusPaletteByLabel.ongoing;
  }

  if (normalized.includes("pending")) {
    return statusPaletteByLabel.pending;
  }

  if (normalized.includes("delay") || normalized.includes("risk")) {
    return statusPaletteByLabel.delayed;
  }

  return statusPaletteByLabel.neutral;
}

function normalizeProjectRow(row) {
  return {
    id: row.lfp_id || row.subaybayan_project_code,
    code: row.subaybayan_project_code || "-",
    title: row.project_name || row.subaybayan_project_code || "Untitled Project",
    province: row.province || "-",
    city: row.city_municipality || "-",
    barangay: row.barangay || "-",
    fundingYear: row.funding_year || "-",
    fundSource: row.fund_source || "-",
    procurementType: row.mode_of_procurement || "-",
    lgsfAllocation: row.lgsf_allocation,
    obligation: row.obligation,
    utilizationRate: Number(row.utilization_rate ?? 0),
    physicalStatus: Number(row.subay_accomplishment_pct ?? row.accomplishment_pct_ro ?? 0),
    statusActual: row.status_actual || "-",
    statusSubaybayan: row.status_subaybayan_current || row.status_subaybayan || "-",
    lastUpdatedAt: row.updated_at,
  };
}

function InfoTile({ label, value }) {
  return (
    <View className="w-[48.5%] rounded-xl border border-[#d0d6df] bg-[#e5e7eb] px-3 py-2">
      <Text className="text-[11px] font-bold uppercase text-[#4b5563]">{label}</Text>
      <Text className="mt-1 text-[18px] font-semibold text-[#374151]">{value}</Text>
    </View>
  );
}

function StatusBadge({ label }) {
  const palette = getStatusPalette(label);

  return (
    <View
      className="rounded-xl px-3 py-1.5"
      style={{ backgroundColor: palette.backgroundColor }}
    >
      <Text
        className="text-[11px] font-bold uppercase"
        style={{ color: palette.color }}
        numberOfLines={1}
      >
        {label}
      </Text>
    </View>
  );
}

export default function LocallyFundedProjectsScreen() {
  const { activeBaseUrl, fetchJsonWithFallback } = useWebAppRequest();
  const [projects, setProjects] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [isRefreshing, setIsRefreshing] = useState(false);
  const [expandedCardId, setExpandedCardId] = useState(null);
  const [errorMessage, setErrorMessage] = useState("");

  const loadProjects = useCallback(
    async (isPullToRefresh = false) => {
      if (isPullToRefresh) {
        setIsRefreshing(true);
      } else {
        setIsLoading(true);
      }

      try {
        setErrorMessage("");

        const payload = await fetchJsonWithFallback(
          "/api/mobile/locally-funded?per_page=50"
        );
        const rows = Array.isArray(payload?.data) ? payload.data : [];
        setProjects(rows.map(normalizeProjectRow));
        setExpandedCardId(null);
      } catch (error) {
        setProjects([]);
        setExpandedCardId(null);

        const hint =
          `Make sure Laravel is running and your phone can reach your computer on the same network. Current base URL: ${API_URL}. You can set EXPO_PUBLIC_API_URL to your PC IP, for example http://192.168.x.x:8000.`;
        setErrorMessage(`${error?.message || "Unable to load projects."}. ${hint}`);
      } finally {
        setIsLoading(false);
        setIsRefreshing(false);
      }
    },
    [fetchJsonWithFallback]
  );

  useEffect(() => {
    loadProjects(false);
  }, [loadProjects]);

  const renderProjectCard = ({ item }) => {
    const isExpanded = expandedCardId === item.id;

    return (
      <View className="mb-3 rounded-3xl border border-[#bfc3c9] bg-[#e6e8ea] px-3 py-3">
        <Pressable
          className="flex-row items-start"
          onPress={() => {
            setExpandedCardId((currentId) => (currentId === item.id ? null : item.id));
          }}
        >
          <View className="flex-1 pr-2">
            <Text className="text-[16px] font-bold text-[#404040]">{item.code}</Text>
            <Text className="mt-1 text-[17px] font-semibold text-[#4b4b4b]">{item.title}</Text>
            <View className="mt-2 border-b border-[#bfc3c9]" />
            <Text className="mt-2 text-[14px] font-semibold text-[#4b4b4b]">
              {item.city}, {item.province}
            </Text>
          </View>

          <View className="h-10 w-10 items-center justify-center rounded-full bg-[#c7d2fe]">
            <Feather name={isExpanded ? "minus" : "plus"} size={22} color="#1e3a8a" />
          </View>
        </Pressable>

        {isExpanded ? (
          <View className="mt-3 rounded-2xl border border-[#cfd5df] bg-[#eceff3] px-3 py-3">
            <View className="flex-row items-start justify-between gap-2">
              <View className="flex-1">
                <Text className="text-[12px] font-bold text-[#6b7280]">{item.code}</Text>
                <Text className="mt-0.5 text-[12px] font-semibold text-[#4b5563]">{item.title}</Text>
                <Text className="mt-1 text-[12px] text-[#4b5563]">
                  {item.city}, {item.province}
                </Text>
                <Text className="text-[12px] font-semibold text-[#4b5563]">{item.barangay}</Text>
              </View>

              <View className="items-end">
                <Text className="text-[10px] font-bold uppercase text-[#6b7280]">Last Updated At</Text>
                <Text className="mt-0.5 text-[10px] font-semibold text-[#4b5563]">
                  {formatUpdatedAt(item.lastUpdatedAt)}
                </Text>
              </View>
            </View>

            <View className="mt-3 flex-row flex-wrap justify-between gap-y-2">
              <InfoTile label="Funding Year" value={String(item.fundingYear)} />
              <InfoTile label="Fund Source" value={String(item.fundSource)} />
              <InfoTile label="Procurement Type" value={String(item.procurementType)} />
              <InfoTile label="LGSF Allocation" value={formatMoney(item.lgsfAllocation)} />
              <InfoTile label="Obligation" value={formatMoney(item.obligation)} />
              <InfoTile label="Utilization Rate" value={formatPercent(item.utilizationRate)} />
            </View>

            <View className="mt-3 rounded-xl border border-[#d0d6df] bg-[#e5e7eb] px-3 py-2">
              <Text className="text-[11px] font-bold uppercase text-[#4b5563]">
                Physical Status (Subaybayan %)
              </Text>
              <View className="mt-2 h-2 w-full rounded-full bg-[#c7d2fe]">
                <View
                  className="h-2 rounded-full bg-[#3b82f6]"
                  style={{ width: `${Math.max(0, Math.min(100, item.physicalStatus))}%` }}
                />
              </View>
              <Text className="mt-1 text-[18px] font-semibold text-[#374151]">
                {formatPercent(item.physicalStatus)}
              </Text>
            </View>

            <View className="mt-3 flex-row flex-wrap justify-between gap-y-2">
              <View className="w-[48.5%] rounded-xl border border-[#d0d6df] bg-[#e5e7eb] px-3 py-2">
                <Text className="text-[11px] font-bold uppercase text-[#4b5563]">Status (Actual)</Text>
                <View className="mt-2 self-start">
                  <StatusBadge label={item.statusActual} />
                </View>
              </View>

              <View className="w-[48.5%] rounded-xl border border-[#d0d6df] bg-[#e5e7eb] px-3 py-2">
                <Text className="text-[11px] font-bold uppercase text-[#4b5563]">
                  Status (Subaybayan)
                </Text>
                <View className="mt-2 self-start">
                  <StatusBadge label={item.statusSubaybayan} />
                </View>
              </View>
            </View>
          </View>
        ) : null}
      </View>
    );
  };

  return (
    <SafeAreaView className="flex-1 bg-[#f1f5f9]" edges={[]}>
      <View className="px-4 pt-4 pb-2">
        <Text className="text-[23px] font-bold text-[#002C76]">Locally Funded Projects</Text>
        <Text className="mt-1 text-[12px] text-[#475569]">
          Source: {activeBaseUrl}/api/mobile/locally-funded
        </Text>
      </View>

      {isLoading ? (
        <View className="flex-1 items-center justify-center px-6">
          <ActivityIndicator size="large" color="#1d4ed8" />
          <Text className="mt-3 text-[13px] text-[#475569]">Loading project cards...</Text>
        </View>
      ) : (
        <FlatList
          contentContainerStyle={{ paddingHorizontal: 12, paddingBottom: 24 }}
          data={projects}
          keyExtractor={(item, index) => `${item.id}-${index}`}
          renderItem={renderProjectCard}
          refreshControl={
            <RefreshControl
              refreshing={isRefreshing}
              onRefresh={() => {
                loadProjects(true);
              }}
              tintColor="#1d4ed8"
            />
          }
          ListEmptyComponent={
            <View className="mt-10 rounded-2xl border border-[#dbe3f0] bg-white px-4 py-5">
              <Text className="text-[15px] font-semibold text-[#1e3a8a]">
                No projects available
              </Text>
              <Text className="mt-1 text-[12px] leading-[18px] text-[#64748b]">
                {errorMessage || "No rows were returned by the endpoint."}
              </Text>
              <Pressable
                className="mt-4 self-start rounded-xl bg-[#dbeafe] px-4 py-2"
                onPress={() => {
                  loadProjects(false);
                }}
              >
                <Text className="text-[12px] font-semibold text-[#1e3a8a]">Retry Fetch</Text>
              </Pressable>
            </View>
          }
        />
      )}
    </SafeAreaView>
  );
}
