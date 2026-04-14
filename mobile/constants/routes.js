export const APP_ROUTES = {
  login: "/",
  homeTab: "/(tabs)/home",
  projectMonitoring: {
    locallyFundedProjects: "/(tabs)/project-monitoring/locally-funded-projects",
    rlipLimeDevelopmentFund: "/(tabs)/project-monitoring/rlip-lime-20-development-fund",
    projectAtRisk: "/(tabs)/project-monitoring/project-at-risk",
    sglgifPortal: "/(tabs)/project-monitoring/sglgif-portal",
  },
};

export const TAB_ROUTES = [
  { route: "home/index", title: "Home", icon: "grid" },
  { route: "message/index", title: "Messages", icon: "message-square" },
  { route: "capture/index", title: "Capture", icon: "camera" },
  { route: "notifications/index", title: "Notifications", icon: "bell" },
  { route: "settings/index", title: "Settings", icon: "settings" },
];

export const PROJECT_MONITORING_ROUTES = [
  {
    route: "project-monitoring/locally-funded-projects",
    title: "Locally Funded Projects",
  },
  {
    route: "project-monitoring/rlip-lime-20-development-fund",
    title: "RLIP/LIME-20% Development Fund",
  },
  {
    route: "project-monitoring/project-at-risk",
    title: "Project At Risk",
  },
  {
    route: "project-monitoring/sglgif-portal",
    title: "SGLGIF Portal",
  },
];
