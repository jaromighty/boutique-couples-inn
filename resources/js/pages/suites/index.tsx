import React, { useState, useMemo } from 'react';
import { Head, router } from '@inertiajs/react';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import SuiteCard from '@/components/suite-card';
import BookingModal from "@/components/booking-modal";

interface SuitePageProps {
    suites: Array<{
        id: number;
        name: string;
        suite_slug: string;
        theme_slug: string;
        tier: string;
        base_price: number;
        capacity: number;
        amenities: string[];
        images: string[];
        is_available: boolean;
        availability_status?: 'available' | 'pending' | 'confirmed' | 'unknown';
        hold_expires_at?: string | null;
        is_active: boolean;
    }>;
    filters: {
        tier?: string;
        theme?: string;
    };
}

export default function SuitesIndex({ suites, filters }: SuitePageProps) {
    const [localTierFilter, setLocalTierFilter] = useState(filters.tier || '');
    const [localThemeFilter, setLocalThemeFilter] = useState(filters.theme || '');
    const [modalOpen, setModalOpen] = useState(false);
    const [selectedSuite, setSelectedSuite] = useState<any>(null);

    // Extract unique themes for the filter dropdown
    const themes = useMemo(() => {
        const uniqueThemes = [...new Set(suites.map(s => s.theme_slug))];
        return uniqueThemes.sort();
    }, [suites]);

    const handleFilterChange = (key: string, value: string) => {
        const newFilters = { ...filters };
        if (value) {
            newFilters[key] = value;
        } else {
            delete newFilters[key];
        }

        // Update local state immediately for responsiveness
        if (key === 'tier') setLocalTierFilter(value);
        if (key === 'theme') setLocalThemeFilter(value);

        // Trigger Inertia GET request with new query params
        router.get(route('suites.index'), newFilters, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const openBooking = (suite: any) => {
        setSelectedSuite(suite);
        setModalOpen(true);
    };

    return (
        <>
            <Head title="Find Your Perfect Romantic Suite" />

            <div className="container mx-auto px-4 py-8 max-w-7xl">
                <header className="mb-8 text-center space-y-2">
                    <h1 className="text-4xl font-serif font-bold text-gray-900">Themed Suites for Couples</h1>
                    <p className="text-gray-600 max-w-2xl mx-auto">
                        Explore our unique collection of themed rooms. Each suite is one-of-a-kind.
                        <br/>
                        <span className="text-sm italic text-orange-600">
                            Note: If a suite shows "Held," someone else is booking it right now. Act fast!
                        </span>
                    </p>
                </header>

                {/* Filters Bar */}
                <div className="flex flex-col md:flex-row gap-4 mb-8 p-4 bg-white rounded-lg shadow-sm border border-gray-100 sticky top-4 z-10">
                    <div className="flex-1">
                        <label className="block text-sm font-medium text-gray-700 mb-1">Filter by Tier</label>
                        <Select
                            value={localTierFilter}
                            onValueChange={(val) => handleFilterChange('tier', val)}
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="All Tiers" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="">All Tiers</SelectItem>
                                <SelectItem value="ruby">Ruby</SelectItem>
                                <SelectItem value="emerald">Emerald</SelectItem>
                                <SelectItem value="diamond">Diamond</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div className="flex-1">
                        <label className="block text-sm font-medium text-gray-700 mb-1">Filter by Theme</label>
                        <Select
                            value={localThemeFilter}
                            onValueChange={(val) => handleFilterChange('theme', val)}
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="All Themes" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="">All Themes</SelectItem>
                                {themes.map(theme => (
                                    <SelectItem key={theme} value={theme}>
                                        {theme.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>

                    <div className="flex items-end">
                        <div className="text-sm text-gray-500 pb-2">
                            Showing {suites.filter(s => s.is_active).length} suites
                        </div>
                    </div>
                </div>

                {/* Grid */}
                {suites.length === 0 ? (
                    <div className="text-center py-12 text-gray-500">
                        No suites found matching your filters.
                    </div>
                ) : (
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        {suites.map((suite) => (
                            <SuiteCard key={suite.id} suite={suite} />
                        ))}
                    </div>
                )}

                {selectedSuite && (
                    <BookingModal
                        isOpen={modalOpen}
                        onClose={() => setModalOpen(false)}
                        suiteSlug={selectedSuite.suite_slug}
                        suiteName={selectedSuite.name}
                        basePrice={selectedSuite.base_price}
                    />
                )}
            </div>
        </>
    );
}
