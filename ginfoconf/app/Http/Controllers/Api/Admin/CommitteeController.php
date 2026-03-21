<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommitteeMember;
use App\Models\Conference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommitteeController extends Controller
{
    private const COMMITTEES = ['scientific', 'academic', 'program', 'organizing'];
    private const ROLES      = ['chair', 'co_chair', 'member'];

    /**
     * GET /admin/conferences/{conference}/committee
     */
    public function index(Conference $conference): JsonResponse
    {
        $this->authorize('manage', $conference);

        $members = $conference->committeeMembers()->get();

        return response()->json($members->groupBy('committee'));
    }

    /**
     * POST /admin/conferences/{conference}/committee
     */
    public function store(Request $request, Conference $conference): JsonResponse
    {
        $this->authorize('manage', $conference);

        $data = $request->validate([
            'committee'   => ['required', 'string', 'in:' . implode(',', self::COMMITTEES)],
            'name'        => ['required', 'string', 'max:255'],
            'role'        => ['nullable', 'string', 'in:' . implode(',', self::ROLES)],
            'email'       => ['nullable', 'email', 'max:255'],
            'affiliation' => ['nullable', 'string', 'max:500'],
            'country'     => ['nullable', 'string', 'size:2'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        $member = $conference->committeeMembers()->create([
            'committee'   => $data['committee'],
            'name'        => $data['name'],
            'role'        => $data['role'] ?? 'member',
            'email'       => $data['email'] ?? null,
            'affiliation' => $data['affiliation'] ?? null,
            'country'     => $data['country'] ?? null,
            'sort_order'  => $data['sort_order'] ?? 0,
        ]);

        return response()->json($member, 201);
    }

    /**
     * PUT /admin/conferences/{conference}/committee/{member}
     */
    public function update(Request $request, Conference $conference, CommitteeMember $member): JsonResponse
    {
        $this->authorize('manage', $conference);

        if ($member->conference_id !== $conference->id) {
            abort(404);
        }

        $data = $request->validate([
            'committee'   => ['sometimes', 'string', 'in:' . implode(',', self::COMMITTEES)],
            'name'        => ['sometimes', 'required', 'string', 'max:255'],
            'role'        => ['nullable', 'string', 'in:' . implode(',', self::ROLES)],
            'email'       => ['nullable', 'email', 'max:255'],
            'affiliation' => ['nullable', 'string', 'max:500'],
            'country'     => ['nullable', 'string', 'size:2'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        $member->update($data);

        return response()->json($member->fresh());
    }

    /**
     * DELETE /admin/conferences/{conference}/committee/{member}
     */
    public function destroy(Conference $conference, CommitteeMember $member): JsonResponse
    {
        $this->authorize('manage', $conference);

        if ($member->conference_id !== $conference->id) {
            abort(404);
        }

        $member->delete();

        return response()->json(null, 204);
    }
}
