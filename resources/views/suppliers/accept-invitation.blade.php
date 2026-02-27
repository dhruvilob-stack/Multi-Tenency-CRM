<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Supplier Invitation</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="min-h-screen bg-slate-50">
        <main class="mx-auto flex min-h-screen max-w-md items-center px-6">
            <div class="w-full rounded-2xl bg-white p-8 shadow-lg">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-slate-900">Activate your supplier account</h1>
                    <p class="mt-2 text-sm text-slate-600">
                        Your email is locked for security. Set a password to continue.
                    </p>
                </div>

                @if ($isExpired || $invitation->accepted_at)
                    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        This invitation has expired or was already used.
                    </div>
                @else
                    <form method="POST" action="{{ route('supplier.invitation.accept', $invitation->token) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="text-sm font-medium text-slate-700">Email</label>
                            <input
                                type="email"
                                value="{{ $invitation->email }}"
                                readonly
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-700"
                            >
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-700">Password</label>
                            <input
                                type="password"
                                name="password"
                                required
                                class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                            >
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-700">Confirm Password</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                required
                                class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                            >
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
                        >
                            Set password and continue
                        </button>
                    </form>
                @endif
            </div>
        </main>
    </body>
</html>
