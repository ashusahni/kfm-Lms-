import { PanelLayout } from "@/components/PanelLayout";

/**
 * Panel wrapper: layout is in PanelLayout; nested routes (Dashboard, Programs, etc.)
 * are defined in App.tsx under path="/panel" and render into PanelLayout’s Outlet.
 */
const Panel = () => <PanelLayout />;

export default Panel;
