import {Head} from "@inertiajs/react";
import {Card, CardContent, CardHeader, CardTitle} from "@/components/ui/card";
import {Clock, DollarSign, Users} from "lucide-react";

interface DashboardProps {
    metrics: { active_holds: number; confirmed_today: number; total_revenue: number };
    upcoming_checkins: any[];
    recent_bookings: any[];
}

export default function Dashboard({ metrics, upcoming_checkins, recent_bookings }: DashboardProps) {
    return (
        <>
            <Head title="Admin Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Active Holds</CardTitle>
                            <Clock className="h-4 w-4 text-orange-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{metrics.active_holds}</div>
                            <p className="text-xs text-muted-foreground">Rooms currently on hold</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Check-ins Today</CardTitle>
                            <Users className="h-4 w-4 text-blue-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{metrics.confirmed_today}</div>
                            <p className="text-xs text-muted-foreground">Guests arriving today</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Revenue</CardTitle>
                            <DollarSign className="h-4 w-4 text-green-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">${metrics.total_revenue.toLocaleString()}</div>
                            <p className="text-xs text-muted-foreground">Lifetime confirmed bookings</p>
                        </CardContent>
                    </Card>
                </div>

                <div className="grid auto-rows-min gap-4 md:grid-cols-2">
                    <Card>
                        <CardHeader><CardTitle>Upcoming Check-ins (7 Days)</CardTitle></CardHeader>
                        <CardContent>
                            {upcoming_checkins.length === 0 ? (
                                <p className="text-muted-foreground">No upcoming check-ins.</p>
                            ) : (
                                <div className="space-y-4">
                                    {upcoming_checkins.map((b) => (
                                        <div key={b.id} className="flex justify-between items-center border-b pb-2 last:border-0">
                                            <div>
                                                <p className="font-medium">{b.suite.name}</p>
                                                <p className="text-sm text-muted-foreground">{b.check_in.format('MMM d, yyyy')}</p>
                                            </div>
                                            <span className="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Confirmed</span>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader><CardTitle>Recent Activity</CardTitle></CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                {recent_bookings.map((b) => (
                                    <div key={b.id} className="flex justify-between items-center text-sm">
                                        <span>{b.suite.name}</span>
                                        <span className={`px-2 py-1 rounded-full text-xs ${
                                            b.status.value === 'confirmed' ? 'bg-green-100 text-green-800' :
                                                b.status.value === 'pending' ? 'bg-orange-100 text-orange-800' :
                                                    'bg-gray-100 text-gray-800'
                                        }`}>
                                        {b.status.label()}
                                    </span>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: '#',
        }
    ],
};
