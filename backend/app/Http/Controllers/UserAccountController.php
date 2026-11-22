<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Support\ProfilePhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserAccountController extends Controller
{
    private const TYPE_LABELS = [
        'student' => 'Mahasiswa',
        'lecturer' => 'Dosen',
        'staff' => 'Staf',
    ];

    public function edit(): View
    {
        return view('pages.user.edit', [
            'typeLabels' => self::TYPE_LABELS,
        ]);
    }

    public function index(): View
    {
        return view('pages.user.dashboard', [
            'reports' => auth()->user()->lostReports()
                ->with(['latestRejection', 'latestReturnRejection'])
                ->latest()
                ->paginate(10),
            'missingReports' => auth()->user()->missingReports()
                ->with('latestRejection')
                ->latest()
                ->paginate(10, ['*'], 'missing_page'),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->safe()->except(['profile_photo', 'phone']);
        $data['phone'] = $request->string('phone')->trim()->toString();

        if ($request->hasFile('profile_photo')) {
            ProfilePhoto::delete($user->profile_photo_path);
            $data['profile_photo_path'] = ProfilePhoto::store($request->file('profile_photo'));
        }

        $user->update($data);

        return redirect()->route('user.dashboard')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function photo(Request $request)
    {
        return ProfilePhoto::response($request->user());
    }
}
