<x-app-layout>
    <x-slot name="title">Setup Notifications</x-slot>

    <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold" style="color: var(--color-text-primary);">Notification Setup</h1>
            <p class="text-sm mt-1" style="color: var(--color-text-secondary);">Manage recipients for system notifications and alerts.</p>
        </div>
        <button onclick="document.getElementById('recipientModal').showModal()" class="btn btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Recipient
        </button>

        <!-- Modal Native HTML5 -->
        <dialog id="recipientModal" class="p-0 bg-transparent rounded-xl shadow-2xl backdrop:bg-black/70 backdrop:backdrop-blur-none" style="margin: auto;">
            <div class="w-full max-w-3xl bg-white rounded-xl text-left overflow-hidden" style="background: var(--color-card-bg); width: 800px; max-width: 95vw;">
                <form action="{{ route('notification-settings.store') }}" method="POST">
                    @csrf
                    <div class="px-6 pt-6 pb-4">
                        <h3 class="text-lg leading-6 font-medium mb-4" id="modal-title" style="color: var(--color-text-primary);">Add New Recipient</h3>
                        <div class="space-y-4 text-left">
                            <div>
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-input" required placeholder="John Doe">
                            </div>
                            <div>
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-input" required placeholder="john@example.com">
                            </div>
                            <div>
                                <label class="form-label">WhatsApp</label>
                                <input type="text" name="whatsapp" class="form-input" placeholder="+6281234567890">
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 flex justify-end gap-3" style="background: rgba(0,0,0,0.02); border-top: 1px solid var(--color-border);">
                        <button type="button" onclick="document.getElementById('recipientModal').close()" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Recipient</button>
                    </div>
                </form>
            </div>
        </dialog>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" 
             x-init="setTimeout(() => show = false, 4000)" 
             x-show="show" 
             x-transition.duration.500ms
             class="fixed top-6 right-6 z-[9999] flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 border border-green-200 shadow-xl" 
             role="alert">
            <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            <div class="font-medium">
                {{ session('success') }}
            </div>
            <button @click="show = false" class="ml-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex h-8 w-8">
                <span class="sr-only">Close</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
        </div>
    @endif

    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-center"><div class="flex justify-center w-full">Name</div></th>
                        <th class="text-center"><div class="flex justify-center w-full">Email</div></th>
                        <th class="text-center"><div class="flex justify-center w-full">WhatsApp</div></th>
                        <th class="text-center"><div class="flex justify-center w-full">Status</div></th>
                        <th class="text-center"><div class="flex justify-center w-full">Action</div></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recipients as $recipient)
                        <tr>
                            <td class="text-center font-medium">{{ $recipient->name }}</td>
                            <td class="text-center">{{ $recipient->email }}</td>
                            <td class="text-center">{{ $recipient->whatsapp ?? '-' }}</td>
                            <td class="text-center">
                                <div class="flex justify-center w-full">
                                    @if($recipient->is_active)
                                        <span class="badge badge--active">Active</span>
                                    @else
                                        <span class="badge badge--neutral">Inactive</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="flex justify-center items-center gap-2 w-full">
                                    <form action="{{ route('notification-settings.update', $recipient->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="name" value="{{ $recipient->name }}">
                                        <input type="hidden" name="email" value="{{ $recipient->email }}">
                                        <input type="hidden" name="whatsapp" value="{{ $recipient->whatsapp }}">
                                        @if($recipient->is_active)
                                            <button type="submit" class="btn btn-secondary text-xs py-1 px-2" title="Deactivate">Disable</button>
                                        @else
                                            <input type="hidden" name="is_active" value="1">
                                            <button type="submit" class="btn btn-primary text-xs py-1 px-2" title="Activate">Enable</button>
                                        @endif
                                    </form>
                                    <form action="{{ route('notification-settings.destroy', $recipient->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this recipient?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost text-red-500 p-1" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-gray-500 font-medium">No recipients found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
