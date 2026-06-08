<?php

namespace App\Http\Controllers;

use App\Models\MediaContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MediaContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contents = MediaContent::where(
            "instance_id",
            auth()->user()->instance_id,
        )
            ->orderBy("created_at", "desc")
            ->get();

        // Calculate statistics
        $totalMedia = $contents->count();
        $activeMedia = $contents->where("is_active", true)->count();
        $imageCount = $contents->where("media_type", "image")->count();
        $videoCount = $contents->where("media_type", "video")->count();

        return view(
            "Pages.StaffKonten.staffContent",
            compact(
                "contents",
                "totalMedia",
                "activeMedia",
                "imageCount",
                "videoCount",
            ),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "title" => "required|string|max:255",
                "file" =>
                    "required|file|mimes:jpg,jpeg,png,gif,mp4,avi,mov|max:51200", // 50MB max
                "duration" => "required|integer|min:1|max:300",
                "is_active" => "boolean",
            ],
            [
                "title.required" => "Judul konten wajib diisi",
                "file.required" => "File media wajib diunggah",
                "file.mimes" =>
                    "Format file harus: JPG, JPEG, PNG, GIF, MP4, AVI, atau MOV",
                "file.max" => "Ukuran file maksimal 50MB",
                "duration.min" => "Durasi minimal 1 detik",
                "duration.max" => "Durasi maksimal 300 detik (5 menit)",
            ],
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with("error", "Gagal menambahkan konten. Periksa form Anda.");
        }

        try {
            $file = $request->file("file");
            $extension = $file->getClientOriginalExtension();

            // Determine media type
            $imageExtensions = ["jpg", "jpeg", "png", "gif"];
            $videoExtensions = ["mp4", "avi", "mov"];

            if (in_array(strtolower($extension), $imageExtensions)) {
                $mediaType = "image";
            } elseif (in_array(strtolower($extension), $videoExtensions)) {
                $mediaType = "video";
            } else {
                return back()->with("error", "Tipe file tidak didukung.");
            }

            // Store file in public storage
            $fileName = time() . "_" . $file->getClientOriginalName();
            $filePath = $file->storeAs("media_contents", $fileName, "public");

            // Create media content record
            MediaContent::create([
                "instance_id" => auth()->user()->instance_id,
                "title" => $request->title,
                "media_type" => $mediaType,
                "file_path" => $filePath,
                "duration" =>
                    $request->duration ?? ($mediaType === "image" ? 10 : null),
                "is_active" => $request->has("is_active") ? true : false,
            ]);

            return redirect()
                ->route("content.index")
                ->with("success", "Konten berhasil ditambahkan!");
        } catch (\Exception $e) {
            return back()->with(
                "error",
                "Terjadi kesalahan: " . $e->getMessage(),
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $instanceSlug, MediaContent $content)
    {
        // Ensure user can only update their own instance's content
        if ($content->instance_id !== auth()->user()->instance_id) {
            return back()->with(
                "error",
                "Anda tidak memiliki akses untuk mengedit konten ini.",
            );
        }

        $validator = Validator::make(
            $request->all(),
            [
                "title" => "required|string|max:255",
                "file" =>
                    "nullable|file|mimes:jpg,jpeg,png,gif,mp4,avi,mov|max:51200",
                "duration" => "required|integer|min:1|max:300",
                "is_active" => "boolean",
            ],
            [
                "title.required" => "Judul konten wajib diisi",
                "file.mimes" =>
                    "Format file harus: JPG, JPEG, PNG, GIF, MP4, AVI, atau MOV",
                "file.max" => "Ukuran file maksimal 50MB",
                "duration.min" => "Durasi minimal 1 detik",
                "duration.max" => "Durasi maksimal 300 detik (5 menit)",
            ],
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with("error", "Gagal mengupdate konten. Periksa form Anda.");
        }

        try {
            $data = [
                "title" => $request->title,
                "duration" => $request->duration,
                "is_active" => $request->has("is_active") ? true : false,
            ];

            // If new file is uploaded
            if ($request->hasFile("file")) {
                // Delete old file
                if (
                    $content->file_path &&
                    Storage::disk("public")->exists($content->file_path)
                ) {
                    Storage::disk("public")->delete($content->file_path);
                }

                // Store new file
                $file = $request->file("file");
                $extension = $file->getClientOriginalExtension();

                // Determine media type
                $imageExtensions = ["jpg", "jpeg", "png", "gif"];
                $videoExtensions = ["mp4", "avi", "mov"];

                if (in_array(strtolower($extension), $imageExtensions)) {
                    $mediaType = "image";
                } elseif (in_array(strtolower($extension), $videoExtensions)) {
                    $mediaType = "video";
                } else {
                    return back()->with("error", "Tipe file tidak didukung.");
                }

                $fileName = time() . "_" . $file->getClientOriginalName();
                $filePath = $file->storeAs(
                    "media_contents",
                    $fileName,
                    "public",
                );

                $data["media_type"] = $mediaType;
                $data["file_path"] = $filePath;
            }

            $content->update($data);

            return redirect()
                ->route("content.index")
                ->with("success", "Konten berhasil diperbarui!");
        } catch (\Exception $e) {
            return back()->with(
                "error",
                "Terjadi kesalahan: " . $e->getMessage(),
            );
        }
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggle(string $instanceSlug, MediaContent $content)
    {
        // Ensure user can only toggle their own instance's content
        if ($content->instance_id !== auth()->user()->instance_id) {
            return response()->json(["error" => "Unauthorized"], 403);
        }

        try {
            $content->update(["is_active" => !$content->is_active]);

            return response()->json([
                "success" => true,
                "message" => $content->is_active
                    ? "Konten diaktifkan"
                    : "Konten dinonaktifkan",
                "is_active" => $content->is_active,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $instanceSlug, MediaContent $content)
    {
        // Ensure user can only delete their own instance's content
        if ($content->instance_id !== auth()->user()->instance_id) {
            return back()->with(
                "error",
                "Anda tidak memiliki akses untuk menghapus konten ini.",
            );
        }

        try {
            // Delete file from storage
            if (
                $content->file_path &&
                Storage::disk("public")->exists($content->file_path)
            ) {
                Storage::disk("public")->delete($content->file_path);
            }

            $content->delete();

            return redirect()
                ->route("content.index")
                ->with("success", "Konten berhasil dihapus!");
        } catch (\Exception $e) {
            return back()->with(
                "error",
                "Terjadi kesalahan: " . $e->getMessage(),
            );
        }
    }
}
