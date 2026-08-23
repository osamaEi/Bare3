<?php

namespace App\Http\Controllers;

use App\Models\TrainerApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class TrainerController extends Controller
{
    /** مسارات الخبرة المتاحة للاختيار (اختيار متعدد). */
    public const TRACKS = [
        'الإبداع والابتكار والتفكير التصميمي',
        'التواصل وفن الإلقاء',
        'اتخاذ القرارات وحل المشكلات',
        'الوعي المالي',
        'الذكاء العاطفي والاجتماعي',
        'المهارة التقنية والذكاء الاصطناعي',
    ];

    public function index(): Response
    {
        $content = app(PublicPageController::class)->sharedContent();

        return Inertia::render('Public/Trainers', [
            'brand'  => $content['brand'],
            'footer' => $content['footer'],
            'tracks' => self::TRACKS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'phone'      => 'required|string|max:30',
            'country'    => 'nullable|string|max:120',
            'tracks'     => 'nullable|array',
            'tracks.*'   => 'string|max:255',
            'linkedin'   => 'nullable|url|max:500',
            'bio'        => 'nullable|string|max:3000',
            'cv'         => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('cv')) {
            $path = $request->file('cv')->store('trainer-cvs', 'public');
            try {
                @chmod(Storage::disk('public')->path($path), 0644);
            } catch (\Throwable) {
                // تجاهل — الرفع نجح على أي حال
            }
            $data['cv_path'] = $path;
        }
        unset($data['cv']);

        TrainerApplication::create($data);

        return back()->with('success', 'تم استلام طلبك بنجاح، وسيتواصل معك فريق بارِع قريباً.');
    }
}
