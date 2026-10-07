import React, { useState, useEffect } from 'react';
import { Link } from '@inertiajs/react';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { Clock, CheckCircle2, AlertCircle } from 'lucide-react';

interface SuiteProps {
    suite: {
        id: number;
        name: string;
        suite_slug: string;
        theme_slug: string;
        tier: string; // 'ruby' | 'emerald' | 'diamond'
        base_price: number;
        capacity: number;
        amenities: string[];
        images: string[];
        is_available: boolean;
        availability_status?: 'available' | 'pending' | 'confirmed' | 'unknown';
        hold_expires_at?: string | null; // ISO string
        is_active: boolean;
    };
}

const TierBadge = ({ tier }: { tier: string }) => {
    const styles = {
        ruby: 'bg-red-100 text-red-800 border-red-200 hover:bg-red-200',
        emerald: 'bg-emerald-100 text-emerald-800 border-emerald-200 hover:bg-emerald-200',
        diamond: 'bg-purple-100 text-purple-800 border-purple-200 hover:bg-purple-200',
    };

    const icons = {
        ruby: '💎',
        emerald: '✨',
        diamond: '💠',
    };

    return (
        <Badge variant="outline" className={`${styles[tier as keyof typeof styles]} font-semibold uppercase tracking-wider`}>
            {icons[tier as keyof typeof icons]} {tier}
        </Badge>
    );
};

export default function SuiteCard({ suite }: SuiteProps) {
    const [timeLeft, setTimeLeft] = useState<string | null>(null);
    const [isExpired, setIsExpired] = useState(false);

    // Countdown Timer Logic for Pending Holds
    useEffect(() => {
        if (suite.availability_status === 'pending' && suite.hold_expires_at) {
            const updateTimer = () => {
                const now = new Date().getTime();
                const expires = new Date(suite.hold_expires_at!).getTime();
                const diff = expires - now;

                if (diff <= 0) {
                    setIsExpired(true);
                    setTimeLeft(null);
                } else {
                    // Format as mm:ss
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                    setTimeLeft(`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
                }
            };

            updateTimer();
            const interval = setInterval(updateTimer, 1000);
            return () => clearInterval(interval);
        }
    }, [suite.availability_status, suite.hold_expires_at]);

    const isPending = suite.availability_status === 'pending';
    const isConfirmed = suite.availability_status === 'confirmed';
    const isUnavailable = !suite.is_available || isExpired;

    // Determine primary image (fallback to first if array exists)
    const primaryImage = suite.images?.[0] || '/images/placeholder-suite.jpg';

    return (
        <Card className={`overflow-hidden transition-all duration-300 hover:shadow-lg flex flex-col h-full ${isUnavailable ? 'opacity-75 grayscale-[0.5]' : ''}`}>
            {/* Image Section */}
            <div className="relative h-48 w-full bg-gray-100">
                <img
                    src={primaryImage}
                    alt={suite.name}
                    className="h-full w-full object-cover"
                />

                {/* Overlay Badges */}
                <div className="absolute top-3 left-3 flex gap-2">
                    <TierBadge tier={suite.tier} />
                </div>

                {/* Availability Overlay */}
                {isPending && (
                    <div className="absolute bottom-0 left-0 right-0 bg-orange-500/90 backdrop-blur-sm p-2 text-center animate-pulse">
                        <span className="flex items-center justify-center gap-2 text-white text-xs font-bold">
                            <Clock size={14} /> HELD! Expires in {timeLeft}
                        </span>
                    </div>
                )}
                {isConfirmed && (
                    <div className="absolute inset-0 bg-black/60 flex items-center justify-center">
                        <div className="text-center text-white">
                            <CheckCircle2 className="mx-auto mb-2 h-8 w-8 text-green-400" />
                            <span className="font-bold text-lg">Booked</span>
                        </div>
                    </div>
                )}
            </div>

            <CardHeader className="pb-2">
                <div className="flex justify-between items-start">
                    <CardTitle className="text-xl font-serif">{suite.name}</CardTitle>
                </div>
                <p className="text-sm text-muted-capitalize capitalize text-gray-500 mt-1">
                    Theme: {suite.theme_slug.replace(/-/g, ' ')}
                </p>
            </CardHeader>

            <CardContent className="flex-grow">
                <div className="space-y-3">
                    <div className="flex items-center justify-between text-sm">
                        <span className="text-gray-600">Capacity:</span>
                        <span className="font-medium">{suite.capacity} Guests</span>
                    </div>

                    <Separator />

                    <div className="flex flex-wrap gap-1">
                        {suite.amenities?.slice(0, 3).map((amenity, idx) => (
                            <Badge key={idx} variant="secondary" className="text-xs">
                                {amenity.replace('_', ' ')}
                            </Badge>
                        ))}
                        {suite.amenities && suite.amenities.length > 3 && (
                            <Badge variant="secondary" className="text-xs px-1">+{suite.amenities.length - 3}</Badge>
                        )}
                    </div>

                    <div className="pt-2">
                        <span className="text-2xl font-bold text-gray-900">${suite.base_price}</span>
                        <span className="text-sm text-gray-500"> / night</span>
                    </div>
                </div>
            </CardContent>

            <CardFooter className="pt-0">
                {isUnavailable ? (
                    <Button className="w-full" disabled variant="secondary">
                        {isExpired ? 'Hold Expired' : isConfirmed ? 'Unavailable' : 'Unavailable'}
                    </Button>
                ) : (
                    <Link href={`/suites/${suite.suite_slug}/book`} className="w-full">
                        <Button className="w-full" variant={isPending ? "destructive" : "default"}>
                            {isPending ? (
                                <>
                                    <AlertCircle className="mr-2 h-4 w-4" /> Secure This Hold
                                </>
                            ) : (
                                'View Details & Book'
                            )}
                        </Button>
                    </Link>
                )}
            </CardFooter>
        </Card>
    );
}
