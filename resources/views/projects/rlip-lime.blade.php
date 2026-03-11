@extends('layouts.dashboard')

@section('title', 'RLIP/LIME-20% Development Fund')
@section('page-title', 'RLIP/LIME-20% Development Fund')

@section('content')
    <div class="content-header">
        <h1>RLIP/LIME-20% Development Fund</h1>
        <p>Track projects funded under RLIP/LIME-20% Development Fund.</p>
    </div>

    @include('projects.partials.project-section-tabs', ['activeTab' => 'rlip-lime'])

    <div style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <!-- Header with Create Button -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="color: #002C76; font-size: 18px; margin: 0;">Projects</h2>
            <a href="{{ route('fund-utilization.create') }}" style="padding: 10px 20px; background-color: #002C76; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: all 0.3s ease;">
                <i class="fas fa-plus"></i> Create Project
            </a>
        </div>
        <p style="margin: 0; color: #6b7280;">Content coming soon.</p>
    </div>
@endsection
