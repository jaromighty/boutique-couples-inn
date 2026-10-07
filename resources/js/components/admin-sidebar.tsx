import {
    Sidebar,
    SidebarContent,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem
} from "@/components/ui/sidebar";
import {Link} from "@inertiajs/react";
import {dashboard} from "@/routes";
import AppLogo from "@/components/app-logo";
import {NavItem} from "@/types";
import {Calendar, LayoutDashboard, Package} from "lucide-react";
import {NavMain} from "@/components/nav-main";

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '#',
        icon: LayoutDashboard
    },
    {
        title: 'Calendar',
        href: '#',
        icon: Calendar
    },
    {
        title: 'Suites',
        href: '#',
        icon: Package
    },
];

export function AdminSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>
        </Sidebar>
    );
}
