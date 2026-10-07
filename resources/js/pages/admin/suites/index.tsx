import {Head, Link} from "@inertiajs/react";
import {Button} from "@/components/ui/button";
import {Edit, Plus, Trash2} from "lucide-react";
import {Badge} from "@/components/ui/badge";

interface Suite {
    id: number;
    name: string;
    tier: string;
    price: number;
    is_active: boolean;
    image_count: number;
}

interface Props {
    suites: Suite[];
}

export default function SuitesIndex() {
    return (
        <>
            <Head title="Manage Suites"/>
            <div className="flex justify-between items-center mb-6">
                <h1 className="text-3xl font-bold">Suite Inventory</h1>
                <Link href={route('admin.suites.create')} className="hidden sm:block">
                    <Button>
                        <Plus className="mr-2 h-4 w-4"/> Add Suite
                    </Button>
                </Link>
            </div>

            <div className="overflow-x-auto">
                <table className="w-full text-left">
                    <thead className="bg-gray-50 border-b">
                    <tr>
                        <th className="p-4 font-medium text-sm">Suite Name</th>
                        <th className="p-4 font-medium text-sm">Tier</th>
                        <th className="p-4 font-medium text-sm">Price/Night</th>
                        <th className="p-4 font-medium text-sm">Images</th>
                        <th className="p-4 font-medium text-sm">Status</th>
                        <th className="p-4 font-medium text-sm text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody className="divide-y">
                    {suites.map((suite) => (
                        <tr key={suite.id} className="hover:bg-gray-50">
                            <td className="p-4 font-medium">{suite.name}</td>
                            <td className="p-4">
                                <Badge variant="outline" className="capitalize">{suite.tier}</Badge>
                            </td>
                            <td className="p-4">${suite.price}</td>
                            <td className="p-4 text-sm text-gray-500">{suite.image_count} files</td>
                            <td className="p-4">
                                <Badge variant={suite.is_active ? 'default' : 'secondary'}>
                                    {suite.is_active ? 'Active' : 'Inactive'}
                                </Badge>
                            </td>
                            <td className="p-4 text-right space-x-2">
                                <Link href={route('admin.suites.edit', suite.id)}>
                                    <Button variant="ghost" size="icon"><Edit size={16}/></Button>
                                </Link>
                                <Button variant="ghost" size="icon" className="text-red-500 hover:text-red-700">
                                    <Trash2 size={16}/>
                                </Button>
                            </td>
                        </tr>
                    ))}
                    </tbody>
                </table>
            </div>

        </>
    );
}

SuitesIndex.layout = {
    breadcrumbs: [
        {
            title: 'Suites',
            href: '#',
        },
    ],
}
