import { useCallback, useEffect, useState } from "react";
import { API_URL } from "../constants/api";
import { useWebAppRequest } from "./useWebAppRequest";

const pesoFormatter = new Intl.NumberFormat("en-PH", {
  style: "currency",
  currency: "PHP",
  maximumFractionDigits: 2,
});

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

export function formatMoney(value) {
  if (value === null || value === undefined || Number.isNaN(Number(value))) {
    return "-";
  }

  return pesoFormatter.format(Number(value));
}

export function formatPercent(value) {
  if (value === null || value === undefined || Number.isNaN(Number(value))) {
    return "0.00%";
  }

  return `${Number(value).toFixed(2)}%`;
}

export function formatUpdatedAt(value) {
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

export function useLocallyFundedProjects() {
  const { activeBaseUrl, fetchJsonWithFallback } = useWebAppRequest();
  const [projects, setProjects] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [isRefreshing, setIsRefreshing] = useState(false);
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
      } catch (error) {
        setProjects([]);

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

  return {
    activeBaseUrl,
    projects,
    isLoading,
    isRefreshing,
    errorMessage,
    loadProjects,
  };
}
