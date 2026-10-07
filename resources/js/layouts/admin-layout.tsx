import {ReactNode} from "react";
import {AppShell} from "@/components/app-shell";
import {AppContent} from "@/components/app-content";
import {AdminSidebarHeader} from "@/components/admin-sidebar-header";
import {AdminSidebar} from "@/components/admin-sidebar";

type AdminLayoutProps = {
    children: ReactNode;
}

export default function AdminLayout({
    children,
}: AdminLayoutProps) {
    return (
        <AppShell variant="sidebar">
            <AdminSidebar />
            <AppContent variant="sidebar" className="min-w-0 overflow-x-clip">
                <AdminSidebarHeader />
                {children}
            </AppContent>
        </AppShell>
    );
}
