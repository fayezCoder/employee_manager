@php
    $typeBadges = [
        'main_command' => 'bg-purple-100 text-purple-800 border-purple-200',
        'branch_command' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
        'center' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'division' => 'bg-blue-100 text-blue-800 border-blue-200',
        'department' => 'bg-amber-100 text-amber-800 border-amber-200',
    ];
    $badgeClass = $typeBadges[$unit->type] ?? 'bg-gray-100 text-gray-800 border-gray-200';
@endphp

<div x-data="{ expanded: true }" class="mb-3">
    <div class="flex items-center justify-between p-3.5 bg-slate-50/80 hover:bg-slate-100/80 border border-slate-200 rounded-xl transition gap-3"
         style="margin-right: {{ $level * 1.5 }}rem;">
        
        <div class="flex items-center gap-3">
            @if($unit->children->count() > 0)
                <button @click="expanded = !expanded" class="w-6 h-6 flex items-center justify-center rounded bg-white border border-gray-200 text-gray-600 hover:text-gray-900 focus:outline-none transition">
                    <svg class="w-3.5 h-3.5 transform transition-transform" :class="expanded ? 'rotate-90' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            @else
                <span class="w-6 h-6 flex items-center justify-center text-gray-300">•</span>
            @endif

            <div>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-gray-900 text-sm">{{ $unit->name }}</span>
                    <span class="text-[11px] font-mono bg-gray-200/70 text-gray-700 px-1.5 py-0.5 rounded">{{ $unit->code }}</span>
                    <span class="text-[11px] font-medium border px-2 py-0.5 rounded-full {{ $badgeClass }}">
                        {{ $unit->type_label }}
                    </span>
                    @if(!$unit->is_active)
                        <span class="text-[11px] font-medium bg-rose-100 text-rose-700 border border-rose-200 px-2 py-0.5 rounded-full">معطلة</span>
                    @endif
                </div>
                <div class="text-[11px] text-gray-500 mt-1 flex items-center gap-3">
                    <span>الموظفون المسجلون: <strong>{{ $unit->employees_count }}</strong></span>
                    @if($unit->children_count > 0)
                        <span>الجهات التابعة: <strong>{{ $unit->children_count }}</strong></span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2">
            @can('super-admin')
                <a href="{{ route('org-units.edit', $unit) }}" class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-white rounded-lg border border-transparent hover:border-gray-200 transition" title="تعديل">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                
                <form method="POST" action="{{ route('org-units.toggle-active', $unit) }}" onsubmit="return confirm('هل أنت متأكد من تغيير حالة تفعيل هذه الجهة؟');" class="inline">
                    @csrf
                    <button type="submit" class="px-2.5 py-1 text-xs font-semibold rounded-lg border transition {{ $unit->is_active ? 'bg-white text-rose-600 hover:bg-rose-50 border-rose-200' : 'bg-emerald-600 text-white hover:bg-emerald-700 border-transparent' }}">
                        {{ $unit->is_active ? 'تعطيل' : 'تفعيل' }}
                    </button>
                </form>
            @endcan
        </div>
    </div>

    <!-- Children Nodes -->
    @if($unit->children->count() > 0)
        <div x-show="expanded" class="mt-2 space-y-2 border-r-2 border-slate-200/60 pr-2 mr-3">
            @foreach($unit->children as $child)
                @include('org_units.partials.tree_node', ['unit' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
