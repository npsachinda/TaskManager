import {Head, router} from '@inertiajs/react';
import {Button} from "@/components/ui/button";
import {Card, CardContent, CardHeader, CardTitle} from "@/components/ui/card";
import {Plus, Pencil, Trash2, CheckCircle2, XCircle, List, CheckCircle, Search, ChevronLeft, ChevronRight} from "lucide-react";
import AppLayout from "@/layouts/app-layout";
import {type BreadcrumbItem} from "@/types";
import React, {useState, useEffect, useCallback} from "react";
import {Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger} from "@/components/ui/dialog";
import {Input} from '@/components/ui/input';
import {Label} from '@/components/ui/label';
import {useForm} from "@inertiajs/react";
import {route} from "ziggy-js";
import {Textarea} from "@headlessui/react";
import {Select, SelectContent, SelectItem, SelectTrigger, SelectValue} from '@/components/ui/select';

interface User {
    id: number;
    name: string;
}

interface Task {
    id: number;
    title: string;
    description: string | null;
    status: string;
    due_date: string | null;
    list_id: number;
    list: {
        id: number;
        title: string;
        user: {
            id: number;
            name: string;
        };
    };
}

interface List {
    id: number;
    title: string;
}

interface Props {
    tasks: {
        data: Task[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
    };
    lists: List[];
    users: User[];
    filters: {
        search: string;
        filter: string;
        user_filter: string;
    };
    flash?: {
        success?: string;
        error?: string;
    };
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tasks',
        href: '/tasks'
    },
];

export default function TaskIndex({tasks, lists, users, filters, flash}: Props) {
    const [isOpen, setIsOpen] = useState(false);
    const [editingTask, setEditingTask] = useState<Task | null>(null);
    const [showToast, setShowToast] = useState(false);
    const [toastMessage, setToastMessage] = useState('');
    const [toastType, setToastType] = useState<'success' | 'error'>('success');
    const [searchTerm, setSearchTerm] = useState(filters.search || '');
    const [completionFilter, setCompletionFilter] = useState<'all' | 'completed' | 'pending'>(
        (filters.filter as 'all' | 'completed' | 'pending') || 'all'
    );
    const [userFilter, setUserFilter] = useState(filters.user_filter || 'all');

    const debouncedSearch = useCallback(
        (search: string, filter: string, user_filter: string) => {
            router.get(
                route('tasks.index'),
                { search, filter, user_filter },
                { preserveState: true, preserveScroll: true }
            );
        },
        []
    );

    useEffect(() => {
        const timer = setTimeout(() => {
            debouncedSearch(searchTerm, completionFilter, userFilter);
        }, 300);

        return () => clearTimeout(timer);
    }, [searchTerm, completionFilter, userFilter, debouncedSearch]);

    useEffect(() => {
        if (flash?.success) {
            setToastMessage(flash.success);
            setToastType('success');
            setShowToast(true);
        } else if (flash?.error) {
            setToastMessage(flash.error);
            setToastType('error');
            setShowToast(true);
        }
    }, [flash]);

    useEffect(() => {
        if (showToast) {
            const timer = setTimeout(() => {
                setShowToast(false);
            }, 3000);
            return () => clearTimeout(timer);
        }

    }, [showToast]);

    const {data, setData, post, put, processing, reset, delete: destroy} = useForm({
        title: '',
        description: '',
        due_date: '',
        list_id: '',
        status: ''
    });

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        if (editingTask) {
            put(route('tasks.update', editingTask.id), {
                onSuccess: () => {
                    setIsOpen(false);
                    reset();
                    setEditingTask(null);
                },
            });
        } else {
            post(route('tasks.store'), {
                onSuccess: () => {
                    setIsOpen(false);
                    reset();
                },
            });
        }
    };
    const handleEdit = (task: Task) => {
        setEditingTask(task);
        setData({
            title: task.title,
            description: task.description || '',
            due_date: task.due_date || '',
            list_id: task.list_id.toString(),
            status: task.status,
        });
        setIsOpen(true);
    };

    const handleDelete = (taskId: number) => {
        destroy(route('tasks.destroy', taskId));
    };

    const handleSearch = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        debouncedSearch(searchTerm, completionFilter, userFilter);
    };

    const handleFilterChange = (value: 'all' | 'completed' | 'pending') => {
        setCompletionFilter(value);
        debouncedSearch(searchTerm, value, userFilter);
    };

    const handleUserFilterChange = (value: string) => {
        setUserFilter(value);
        debouncedSearch(searchTerm, completionFilter, value);
    };

    const handlePageChange = (page: number) => {
        router.get(route('tasks.index'), {
            page,
            search: searchTerm,
            filter: completionFilter,
            user_filter: userFilter
        }, {
            preserveState: true,
            preserveScroll: true
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Tasks"/>
            <div
                className="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-gradient-to-br from-background to-muted/20">
                {showToast && (
                    <div
                        className={`fixed top-4 right-4 z-50 flex items-center gap-2 rounded-lg p-4 shadow-lg ${
                            toastType === 'success' ? 'bg-green-500' : 'bg-red-500'
                        } text-white animate-in fade-in slide-in-from-top-5`}
                    >
                        {toastType === 'success' ? (
                            <CheckCircle2 className="h-5 w-5"/>
                        ) : (
                            <XCircle className="h-5 w-5"/>
                        )}
                        <span>{toastMessage}</span>
                    </div>
                )}

                <div className="flex justify-between items-center">
                    <h1 className="text-2xl font-bold">Tasks</h1>
                    <p className="text-muted-foreground mt-1">Manage your Task and stay organized</p>
                    <Dialog open={isOpen} onOpenChange={setIsOpen}>
                        <DialogTrigger asChild>
                            <Button className="bg-primary hover:bg-primary/90 text-white shadow-lg">
                                <Plus className="h-4 w-4 mr-2"/>
                                New Task
                            </Button>
                        </DialogTrigger>
                        <DialogContent className="sm:max-w-[425px]">
                            <DialogHeader>
                                <DialogTitle
                                    className="text-xl">{editingTask ? 'Edit Task' : 'Create New Task'}</DialogTitle>
                            </DialogHeader>
                            <form onSubmit={handleSubmit} className="space-y-4">
                                <div className="space-y-2">
                                    <Label htmlFor="title">Title</Label>
                                    <Input
                                        id="title"
                                        value={data.title}
                                        onChange={(e) => setData('title', e.target.value)}
                                        required className="focus:ring-2 focu-ring-primary"
                                    />
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="description">Description</Label>
                                    <Textarea
                                        id="description"
                                        value={data.description}
                                        onChange={(e) => setData('description', e.target.value)}
                                        className="focus:ring-2 focu-ring-primary"
                                    />
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="list_id">List</Label>
                                    <Select
                                        value={data.list_id}
                                        onValueChange={(value) => setData('list_id', value)}
                                    >
                                        <SelectTrigger className="focus:ring-2 focus:ring-primary">
                                            <SelectValue placeholder="Select a list"/>
                                        </SelectTrigger>
                                        <SelectContent>
                                            {lists.map((list) => (
                                                <SelectItem key={list.id} value={list.id.toString()}>
                                                    {list.title}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="due_date">Due Date</Label>
                                    <Input
                                        id="due_date"
                                        type="date"
                                        value={data.due_date}
                                        onChange={(e) => setData('due_date', e.target.value)}
                                        className="focus:ring-2 focu-ring-primary"
                                    />
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="status">Task Status</Label>
                                    <Select
                                        value={data.status}
                                        onValueChange={(value) => setData('status', value)}
                                    >
                                        <SelectTrigger className="focus:ring-2 focus:ring-primary">
                                            <SelectValue placeholder="Select a status"/>
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="pending">Pending</SelectItem>
                                            <SelectItem value="completed">Completed</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <Button type="submit" disabled={processing}
                                        className="w-full bg-primary hover:bg-primary/90 text-white shadow-lg">
                                    {editingTask ? 'Update' : 'Create'}
                                </Button>
                            </form>
                        </DialogContent>
                    </Dialog>
                </div>

                <div className="flex gap-4 mb-4">
                    <form onSubmit={handleSearch} className="relative flex-1">
                        <Search
                            className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text text-muted-foreground"/>
                        <Input placeholder="Search tasks..."
                               value={searchTerm}
                               onChange={(e) => setSearchTerm(e.target.value)}
                               className="pl-10"
                        />
                    </form>
                    <Select value={completionFilter}
                            onValueChange={handleFilterChange}>
                        <SelectTrigger className="w-[180px]">
                            <SelectValue placeholder="Select a status"/>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Tasks</SelectItem>
                            <SelectItem value="pending">Pending</SelectItem>
                            <SelectItem value="completed">Completed</SelectItem>
                        </SelectContent>
                    </Select>

                    <Select value={userFilter}
                            onValueChange={handleUserFilterChange}>
                        <SelectTrigger className="w-[180px]">
                            <SelectValue placeholder="Filter by user"/>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Users</SelectItem>
                            {users.map((user) => (
                                <SelectItem key={user.id} value={user.id.toString()}>
                                    {user.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>

                <div className="rounded-md border overflow-x-auto">
                    <table className="min-w-full divide-y divide-gray-200">
                        <thead className="bg-gray-50">
                        <tr>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Title
                            </th>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                List
                            </th>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Assigned To
                            </th>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Due Date
                            </th>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody className="bg-white divide-y divide-gray-200">
                        {tasks.data.map((task) => (
                            <tr key={task.id}>
                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {task.title}
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {task.description}
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {task.list.title}
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {task.list.user.name}
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {task.due_date}
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap">
                        <span
                            className={`inline-flex px-2 text-xs leading-5 font-semibold rounded-full ${
                                task.status === 'Completed'
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-yellow-100 text-yellow-800'
                            }`}
                        >
                            {task.status}
                        </span>
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <button
                                        onClick={() => handleEdit(task)}
                                        className="text-blue-600 hover:text-blue-900 mr-3"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        onClick={() => handleDelete(task.id)}
                                        className="text-red-600 hover:text-red-900"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        ))}
                        </tbody>
                    </table>

                    {/* Pagination */}
                    <div className="flex items-center justify-between px-4 py-3 bg-gray-50 border-t border-gray-200">
                        <div className="flex-1 flex justify-between sm:hidden">
                            <button className="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Previous
                            </button>
                            <button className="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Next
                            </button>
                        </div>
                        <div className="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <p className="text-sm text-gray-700">
                                Showing <span className="font-medium">{tasks.from}</span> to <span className="font-medium">{tasks.to}</span> of <span className="font-medium">{tasks.total}</span> results
                            </p>
                            <div>
                                <nav className="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <Button
                                        onClick={()=>handlePageChange(tasks.current_page-1)}
                                        disabled={tasks.current_page ===1}
                                        className="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                    >
                                        <ChevronLeft className="h-5 w-5" aria-hidden="true" />
                                    </Button>
                                    <Button
                                        onClick={()=>handlePageChange(tasks.current_page+1)}
                                        className="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                    >
                                        <ChevronRight className="h-5 w-5" aria-hidden="true" />
                                    </Button>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </AppLayout>
    )
        ;

}
