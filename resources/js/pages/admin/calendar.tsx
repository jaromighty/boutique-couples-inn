import {Head} from "@inertiajs/react";
import {Button} from "@/components/ui/button";
import {Badge} from "@/components/ui/badge";

interface Event {
    id: number;
    title: string;
    start: string;
    end: string;
    extendedProps: { status: string; guest: string; price: number };
}

interface Props {
    events: Event[];
}

export default function Calendar({ events }: Props) {
    const sortedEvents = [...events].sort((a, b) => new Date(a.start).getTime() - new Date(b.start).getTime());

    return (
        <>
            <Head title="Booking Calendar" />
            {sortedEvents.length === 0 ? (
                <div className="p-8 text-center text-muted-foreground">No bookings found.</div>
            ) : (
                <div className="divide-y">
                    {sortedEvents.map((evt) => (
                        <div key={evt.id} className="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50">
                            <div className="flex items-start gap-4">
                                <div className="flex flex-col items-center min-w-[80px]">
                                    <span className="text-sm font-bold text-gray-900">{format(new Date(evt.start), 'MMM dd')}</span>
                                    <span className="text-xs text-gray-500">{format(new Date(evt.start), 'EEE')}</span>
                                </div>
                                <div>
                                    <h3 className="font-semibold text-lg">{evt.title.split(' (')[0]}</h3>
                                    <p className="text-sm text-gray-600">
                                        {format(new Date(evt.start), 'MMM d')} - {format(new Date(evt.end), 'MMM d, yyyy')}
                                    </p>
                                    <p className="text-xs text-gray-500 mt-1">Guest: {evt.extendedProps.guest} | ${evt.extendedProps.price}</p>
                                </div>
                            </div>
                            <div className="flex items-center gap-2">
                                <Badge
                                    variant={evt.extendedProps.status === 'confirmed' ? 'default' : 'secondary'}
                                    className={evt.extendedProps.status === 'pending' ? 'bg-orange-500 text-white' : ''}
                                >
                                    {evt.extendedProps.status.toUpperCase()}
                                </Badge>

                                {evt.extendedProps.status === 'pending' && (
                                    <Button
                                        size="sm"
                                        variant="outline"
                                        onClick={() => {
                                            // Call API to release
                                            fetch(route('admin.bookings.release', evt.id), { method: 'POST' })
                                                .then(() => window.location.reload());
                                        }}
                                    >
                                        Release Hold
                                    </Button>
                                )}
                                {evt.extendedProps.status === 'confirmed' && (
                                    <Button
                                        size="sm"
                                        variant="destructive"
                                        onClick={() => {
                                            if(confirm('Cancel this booking?')) {
                                                fetch(route('admin.bookings.cancel', evt.id), { method: 'POST' })
                                                    .then(() => window.location.reload());
                                            }
                                        }}
                                    >
                                        Cancel
                                    </Button>
                                )}
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </>
    );
}

Calendar.layout = {
    breadcrumbs: [
        {
            title: 'Booking Calendar',
            href: '#',
        }
    ],
};
