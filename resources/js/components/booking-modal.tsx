import React, { useState, useEffect, useCallback } from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/components/ui/dialog';
import { Calendar } from '@/components/ui/calendar';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
import { AlertCircle, CheckCircle2, Loader2 } from 'lucide-react';
import { format, isSameDay, isWithinInterval, startOfDay, endOfDay } from 'date-fns';
import { router } from '@inertiajs/react';
import Echo from "laravel-echo";

interface BlockedRange {
    start: string;
    end: string;
    type: 'confirmed' | 'pending';
}

interface BookingModalProps {
    isOpen: boolean;
    onClose: () => void;
    suiteSlug: string;
    suiteName: string;
    basePrice: number;
}

export default function BookingModal({ isOpen, onClose, suiteSlug, suiteName, basePrice }: BookingModalProps) {
    const [date, setDate] = useState<{ from: Date; to: Date } | undefined>(undefined);
    const [blockedRanges, setBlockedRanges] = useState<BlockedRange[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    // Hold State
    const [holdData, setHoldData] = useState<any>(null);
    const [countdown, setCountdown] = useState<number>(0);
    const [isProcessing, setIsProcessing] = useState(false);

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });

// Inside your Component
    useEffect(() => {
        if (!suiteSlug) return;

        const channel = window.Echo.channel(`suites.${suiteSlug}`);

        channel.listen('.hold.created', (data) => {
            console.log('Hold created!', data);
            // Force a re-fetch of availability or update local state
            // Ideally, trigger a router.reload() or refetch the API
            router.reload();
        });

        return () => {
            window.Echo.leaveChannel(`suites.${suiteSlug}`);
        };
    }, [suiteSlug]);

    // Fetch blocked dates when modal opens
    useEffect(() => {
        if (isOpen && suiteSlug) {
            setLoading(true);
            fetch(`/api/suites/${suiteSlug}/availability`)
                .then(res => res.json())
                .then(data => {
                    setBlockedRanges(data.blocked_ranges || []);
                    setLoading(false);
                })
                .catch(() => {
                    setError("Could not load availability.");
                    setLoading(false);
                });
        }
    }, [isOpen, suiteSlug]);

    // Countdown Timer Logic
    useEffect(() => {
        let interval: NodeJS.Timeout;
        if (holdData && countdown > 0) {
            interval = setInterval(() => {
                setCountdown(prev => {
                    if (prev <= 1) {
                        clearInterval(interval);
                        // Hold expired logic
                        setHoldData(null);
                        alert("Your hold has expired. Please select dates again.");
                        return 0;
                    }
                    return prev - 1;
                });
            }, 1000);
        }
        return () => clearInterval(interval);
    }, [holdData, countdown]);

    // Helper to check if a day is disabled
    const isDayDisabled = useCallback((day: Date) => {
        if (!date?.from) return false; // Only disable relative to selection if we want, or globally

        // Disable past dates
        if (day < startOfDay(new Date())) return true;

        // Disable blocked ranges
        return blockedRanges.some(range => {
            const rangeStart = startOfDay(new Date(range.start));
            const rangeEnd = endOfDay(new Date(range.end));
            return isWithinInterval(day, { start: rangeStart, end: rangeEnd });
        });
    }, [blockedRanges, date]);

    const handleSelect = (selectedDate: Date | undefined) => {
        if (!selectedDate) return;

        // If clicking the same start date, reset
        if (date?.from && isSameDay(selectedDate, date.from)) {
            setDate(undefined);
            return;
        }

        // If no start date, set start
        if (!date?.from) {
            setDate({ from: selectedDate, to: undefined });
            return;
        }

        // If start exists, set end
        if (date.from && !date.to) {
            if (selectedDate <= date.from) {
                // If clicked earlier date, swap
                setDate({ from: selectedDate, to: date.from });
            } else {
                setDate({ from: date.from, to: selectedDate });
            }
        }
    };

    const handleSubmitHold = async () => {
        if (!date?.from || !date?.to) return;

        setIsProcessing(true);
        setError(null);

        try {
            const payload = {
                check_in: format(date.from, 'yyyy-MM-dd'),
                check_out: format(date.to, 'yyyy-MM-dd'),
                guest_email: 'guest@example.com', // Replace with actual form input later
                guest_name: 'Guest User',         // Replace with actual form input later
            };

            const response = await fetch(`/api/suites/${suiteSlug}/hold`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || "Failed to create hold");
            }

            // Success!
            setHoldData(data.booking);
            setCountdown(data.booking.countdown_seconds);
            setDate(undefined); // Clear selection to show success state
        } catch (err: any) {
            setError(err.message);
            // If 409 Conflict, maybe refresh blocked dates
            if (err.message.includes("held")) {
                // Refresh availability
                const res = await fetch(`/api/suites/${suiteSlug}/availability`);
                const newData = await res.json();
                setBlockedRanges(newData.blocked_ranges || []);
            }
        } finally {
            setIsProcessing(false);
        }
    };

    const handleConfirmPayment = () => {
        // In a real app, redirect to payment gateway or open Stripe modal
        // For MVP, we simulate success
        router.post(`/bookings/${holdData.id}/confirm`, {}, {
            onSuccess: () => {
                setHoldData(null);
                onClose();
                window.location.reload(); // Or show success toast
            }
        });
    };

    const nights = date?.from && date?.to ? Math.ceil(Math.abs(date.to.getTime() - date.from.getTime()) / (1000 * 60 * 60 * 24)) : 0;
    const totalPrice = nights * basePrice;

    return (
        <Dialog open={isOpen} onOpenChange={onClose}>
            <DialogContent className="sm:max-w-[500px]">
                <DialogHeader>
                    <DialogTitle>{suiteName}</DialogTitle>
                    <DialogDescription>
                        Select your dates. Once confirmed, you have 15 minutes to complete payment.
                    </DialogDescription>
                </DialogHeader>

                {/* State 1: Calendar Selection */}
                {!holdData && (
                    <div className="space-y-4">
                        {error && (
                            <div className="bg-red-50 text-red-600 p-3 rounded-md text-sm flex items-center gap-2">
                                <AlertCircle size={16} /> {error}
                            </div>
                        )}

                        <div className="border rounded-lg p-4 bg-gray-50">
                            <Calendar
                                mode="range"
                                selected={date}
                                onSelect={handleSelect}
                                disabled={isDayDisabled}
                                numberOfMonths={1}
                                className="mx-auto"
                            />
                        </div>

                        {date?.from && date?.to && (
                            <div className="bg-blue-50 p-4 rounded-md border border-blue-100">
                                <div className="flex justify-between items-center mb-2">
                                    <span className="font-medium">Selected Dates:</span>
                                    <span className="text-sm text-gray-600">
                                        {format(date.from, 'MMM d')} - {format(date.to, 'MMM d, yyyy')}
                                    </span>
                                </div>
                                <div className="flex justify-between items-center font-bold text-lg">
                                    <span>Total ({nights} nights):</span>
                                    <span>${totalPrice}</span>
                                </div>
                            </div>
                        )}

                        <DialogFooter>
                            <Button variant="outline" onClick={onClose}>Cancel</Button>
                            <Button
                                onClick={handleSubmitHold}
                                disabled={!date?.from || !date?.to || isProcessing}
                            >
                                {isProcessing && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                                Hold Suite (15 min)
                            </Button>
                        </DialogFooter>
                    </div>
                )}

                {/* State 2: Active Hold (Countdown) */}
                {holdData && (
                    <div className="space-y-6 text-center py-4">
                        <div className="bg-green-50 p-4 rounded-full inline-block">
                            <CheckCircle2 className="h-12 w-12 text-green-500" />
                        </div>

                        <div>
                            <h3 className="text-xl font-bold text-gray-900">Suite Held!</h3>
                            <p className="text-gray-600 mt-1">
                                You have secured <strong>{suiteName}</strong> for {nights} nights.
                            </p>
                        </div>

                        <div className="space-y-2">
                            <div className="flex justify-between text-sm font-medium">
                                <span>Time Remaining:</span>
                                <span className="text-red-600 font-mono">{Math.floor(countdown / 60)}:{(countdown % 60).toString().padStart(2, '0')}</span>
                            </div>
                            <Progress value={(countdown / 900) * 100} className="h-2" />
                            <p className="text-xs text-gray-500">
                                This room will be released if payment is not completed.
                            </p>
                        </div>

                        <DialogFooter className="sm:justify-center">
                            <Button onClick={handleConfirmPayment} className="w-full sm:w-auto">
                                Complete Payment Now
                            </Button>
                            <Button variant="ghost" onClick={() => { setHoldData(null); }} className="mt-2">
                                Cancel Hold
                            </Button>
                        </DialogFooter>
                    </div>
                )}
            </DialogContent>
        </Dialog>
    );
}
