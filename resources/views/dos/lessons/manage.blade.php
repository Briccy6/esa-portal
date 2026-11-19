@extends('dos.layout')

@section('page_title', 'Lessons')

@section('content')
<style>
    .lesson-management {
        --lesson-ink: #172b4d;
        --lesson-muted: #64748b;
        --lesson-border: #e2e8f0;
        max-width: 1440px;
        margin: 0 auto;
        padding: clamp(1rem, 3vw, 2rem);
    }

    .lesson-page-header,
    .lesson-list-header,
    .lesson-row,
    .lesson-row-main,
    .lesson-row-actions {
        display: flex;
        align-items: center;
    }

    .lesson-page-header,
    .lesson-list-header {
        justify-content: space-between;
        gap: 1rem;
    }

    .lesson-page-header {
        margin-bottom: 1.5rem;
    }

    .lesson-page-header h1 {
        margin: 0;
        color: var(--lesson-ink);
        font-size: clamp(1.5rem, 3vw, 2rem);
        font-weight: 700;
    }

    .lesson-page-header p,
    .lesson-list-header p {
        margin: .35rem 0 0;
        color: var(--lesson-muted);
    }

    .lesson-panel {
        overflow: hidden;
        border: 1px solid var(--lesson-border);
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 8px 26px rgba(15, 23, 42, .06);
    }

    .lesson-list-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--lesson-border);
    }

    .lesson-list-header h2 {
        margin: 0;
        color: var(--lesson-ink);
        font-size: 1.1rem;
        font-weight: 700;
    }

    .lesson-toolbar {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--lesson-border);
        background: #f8fafc;
    }

    .lesson-search {
        position: relative;
        flex: 1;
        max-width: 42rem;
    }

    .lesson-search .form-control {
        min-height: 46px;
        padding-left: 2.75rem;
        border-color: #cbd5e1;
    }

    .lesson-search-icon {
        position: absolute;
        z-index: 3;
        top: 50%;
        left: 1rem;
        color: var(--lesson-muted);
        transform: translateY(-50%);
    }

    .lesson-search-status {
        min-width: 8rem;
        color: var(--lesson-muted);
        font-size: .9rem;
        text-align: right;
    }

    .lesson-row {
        justify-content: space-between;
        gap: 1.5rem;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--lesson-border);
    }

    .lesson-row:last-child {
        border-bottom: 0;
    }

    .lesson-row-main {
        min-width: 0;
        flex: 1;
        justify-content: space-between;
        gap: 2rem;
    }

    .lesson-title-block,
    .lesson-branches-block {
        min-width: 0;
    }

    .lesson-title-block {
        flex: 1;
    }

    .lesson-title-block h3 {
        margin: 0;
        color: var(--lesson-ink);
        font-size: 1rem;
        font-weight: 650;
        overflow-wrap: anywhere;
    }

    .lesson-branches-block {
        width: min(44%, 28rem);
    }

    .lesson-field-label {
        display: block;
        margin-bottom: .45rem;
        color: var(--lesson-muted);
        font-size: .75rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .lesson-branch-list {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
    }

    .lesson-branch-badge {
        display: inline-flex;
        max-width: 100%;
        padding: .3rem .65rem;
        border: 1px solid #d6e2f2;
        border-radius: 999px;
        background: #eff6ff;
        color: #1e467f;
        font-size: .82rem;
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    .lesson-row-actions {
        flex-shrink: 0;
        gap: .5rem;
    }

    .lesson-pagination {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--lesson-border);
    }

    .lesson-pagination:empty {
        display: none;
    }

    .lesson-empty {
        padding: 3rem 1.5rem;
        color: var(--lesson-muted);
        text-align: center;
    }

    .lesson-empty i {
        display: block;
        margin-bottom: .75rem;
        color: #94a3b8;
        font-size: 2rem;
    }

    .assignment-card {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr)) auto;
        align-items: center;
        gap: 1rem;
        margin-bottom: .75rem;
        padding: 1rem;
        border: 1px solid var(--lesson-border);
        border-radius: .75rem;
        background: #fff;
    }

    .assignment-card-label {
        display: block;
        margin-bottom: .2rem;
        color: var(--lesson-muted);
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .assignment-card-value {
        color: var(--lesson-ink);
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    .assignment-editor-row {
        margin-bottom: .75rem;
        padding: 1rem;
        border: 1px solid var(--lesson-border);
        border-radius: .75rem;
        background: #f8fafc;
    }

    .assignment-editor-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr)) auto;
        align-items: end;
        gap: .75rem;
    }

    .assignment-empty {
        padding: 1rem;
        border: 1px dashed #cbd5e1;
        border-radius: .75rem;
        color: var(--lesson-muted);
        text-align: center;
    }

    @media (max-width: 767.98px) {
        .lesson-management {
            padding: 1rem;
        }

        .lesson-page-header,
        .lesson-list-header {
            align-items: stretch;
            flex-direction: column;
        }

        .lesson-page-header .btn {
            width: 100%;
        }

        .lesson-toolbar {
            align-items: stretch;
            flex-direction: column;
            gap: .5rem;
            padding: 1rem;
        }

        .lesson-search {
            max-width: none;
        }

        .lesson-search-status {
            min-width: 0;
            text-align: left;
        }

        .lesson-row,
        .lesson-row-main {
            align-items: stretch;
            flex-direction: column;
            gap: 1rem;
        }

        .lesson-row {
            padding: 1rem;
        }

        .lesson-branches-block {
            width: 100%;
        }

        .lesson-row-actions .btn {
            width: 100%;
        }

        .assignment-card,
        .assignment-editor-grid {
            grid-template-columns: 1fr;
        }

        .assignment-card .btn,
        .assignment-editor-grid .btn {
            width: 100%;
        }

        .lesson-pagination {
            overflow-x: auto;
            padding: 1rem;
        }
    }
</style>

<main class="lesson-management">
    <header class="lesson-page-header">
        <div>
            <h1>Lessons</h1>
            <p>Review lesson branches and manage course and teacher assignments.</p>
        </div>
        <a href="{{ route('dos.lessons.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2" aria-hidden="true"></i>Create lesson
        </a>
    </header>

    <div id="lessonPageMessage" class="mb-3" role="status" aria-live="polite"></div>

    <section class="lesson-panel" aria-labelledby="lessonListHeading">
        <div class="lesson-list-header">
            <div>
                <h2 id="lessonListHeading">Lesson list</h2>
                <p>Use Details to view assignments or add another branch, course, or teacher.</p>
            </div>
        </div>

        <div class="lesson-toolbar">
            <label class="lesson-search" for="lessonSearch">
                <i class="bi bi-search lesson-search-icon" aria-hidden="true"></i>
                <input
                    type="search"
                    class="form-control"
                    id="lessonSearch"
                    value="{{ request('search') }}"
                    placeholder="Search lessons, branches, courses, or teachers"
                    autocomplete="off"
                >
            </label>
            <div id="lessonSearchStatus" class="lesson-search-status" aria-live="polite">
                {{ $lessonDetails->total() }} {{ \Illuminate\Support\Str::plural('lesson', $lessonDetails->total()) }}
            </div>
        </div>

        <div id="lessonRows" aria-live="polite">
            @include('dos.lessons.partials.rows')
        </div>

        <div id="lessonPagination" class="lesson-pagination">
            @include('dos.lessons.partials.pagination')
        </div>
    </section>
</main>

<div class="modal fade" id="lessonDetailsModal" tabindex="-1" aria-labelledby="lessonDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title fs-5" id="lessonDetailsModalLabel">Lesson details</h2>
                    <p class="mb-0 mt-1 text-body-secondary" id="modalLessonTitle"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="assignmentForm" method="POST" action="{{ route('dos.lessons.assign', [], false) }}">
                @csrf
                <input type="hidden" name="lesson_id" id="modalLessonId">
                <div class="modal-body">
                    <div id="assignmentFeedback" class="mb-3" role="status" aria-live="polite"></div>

                    <section class="mb-4" aria-labelledby="currentAssignmentsHeading">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="h6 mb-0" id="currentAssignmentsHeading">Current assignments</h3>
                            <span class="badge text-bg-light" id="assignmentCount">0</span>
                        </div>
                        <div id="currentAssignments" aria-live="polite">
                            <div class="assignment-empty">Loading assignments...</div>
                        </div>
                    </section>

                    <section aria-labelledby="newAssignmentsHeading">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                            <div>
                                <h3 class="h6 mb-1" id="newAssignmentsHeading">Add assignments</h3>
                                <p class="small text-body-secondary mb-0">Choose a branch first, then select its course and teacher.</p>
                            </div>
                            <button type="button" id="addAssignmentButton" class="btn btn-outline-primary btn-sm flex-shrink-0">
                                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Add row
                            </button>
                        </div>
                        <div id="newAssignments"></div>
                    </section>
                </div>

                <div class="modal-footer justify-content-between">
                    <div class="d-flex gap-2">
                        <a id="editLessonLink" href="#" class="btn btn-outline-secondary">Edit lesson</a>
                        <button type="button" id="deleteLessonButton" class="btn btn-outline-danger">Delete lesson</button>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveAssignmentsButton">
                            <span class="save-label">Save assignments</span>
                            <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(() => {
    const branches = @json($branches);
    const teachers = @json($teachers);
    const listUrl = window.location.pathname;
    const assignmentsUrl = @json(route('dos.lessons.assignments', ['lesson' => '__LESSON_ID__'], false));
    const deleteAssignmentUrl = @json(route('dos.lessons.delete-assignment', ['assignmentId' => '__ASSIGNMENT_ID__'], false));
    const deleteLessonUrl = @json(route('dos.lessons.destroy', ['lesson' => '__LESSON_ID__'], false));
    const editLessonUrl = @json(route('dos.lessons.edit', ['lesson' => '__LESSON_ID__'], false));
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const searchInput = document.getElementById('lessonSearch');
    const rowsContainer = document.getElementById('lessonRows');
    const paginationContainer = document.getElementById('lessonPagination');
    const searchStatus = document.getElementById('lessonSearchStatus');
    const feedback = document.getElementById('assignmentFeedback');
    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('lessonDetailsModal'));
    const assignmentForm = document.getElementById('assignmentForm');
    const currentAssignments = document.getElementById('currentAssignments');
    const newAssignments = document.getElementById('newAssignments');
    let activeLesson = null;
    let searchTimer;
    let listRequest;
    let assignmentRowIndex = 0;
    let currentPage = Number(new URLSearchParams(location.search).get('page') || 1);

    function showMessage(container, message, type = 'success') {
        container.replaceChildren();
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} py-2 mb-0`;
        alert.textContent = message;
        container.appendChild(alert);
    }

    async function readJson(response) {
        const data = await response.json().catch(() => null);
        if (!response.ok) {
            const validationError = data?.errors ? Object.values(data.errors).flat()[0] : null;
            throw new Error(validationError || data?.message || 'The request could not be completed.');
        }
        return data;
    }

    function updateBrowserUrl(search, page) {
        const url = new URL(location.href);
        search ? url.searchParams.set('search', search) : url.searchParams.delete('search');
        page > 1 ? url.searchParams.set('page', page) : url.searchParams.delete('page');
        history.replaceState({}, '', url);
    }

    async function refreshLessonList(page = 1, updateUrl = true) {
        listRequest?.abort();
        listRequest = new AbortController();

        const search = searchInput.value.trim();
        const params = new URLSearchParams();
        if (search) params.set('search', search);
        if (page > 1) params.set('page', page);
        const query = params.toString();
        searchStatus.textContent = 'Loading lessons...';

        try {
            const response = await fetch(query ? `${listUrl}?${query}` : listUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                signal: listRequest.signal
            });
            const data = await readJson(response);

            if (page > data.last_page && data.last_page > 0) {
                return refreshLessonList(data.last_page, updateUrl);
            }

            rowsContainer.innerHTML = data.rows;
            paginationContainer.innerHTML = data.pagination;
            currentPage = page;
            searchStatus.textContent = data.total === 0
                ? 'No lessons found'
                : `Showing ${data.from}-${data.to} of ${data.total} lessons`;
            if (updateUrl) updateBrowserUrl(search, page);
        } catch (error) {
            if (error.name === 'AbortError') return;
            searchStatus.textContent = 'Unable to load lessons';
            showMessage(document.getElementById('lessonPageMessage'), error.message, 'danger');
            console.error('Lesson list request failed:', error);
        }
    }

    function appendOptions(select, items, labelProperty, placeholder) {
        select.replaceChildren(new Option(placeholder, ''));
        items.forEach(item => select.add(new Option(item[labelProperty], item.id)));
    }

    function makeField(labelText, select) {
        const wrapper = document.createElement('div');
        const label = document.createElement('label');
        label.className = 'form-label small fw-semibold';
        label.textContent = labelText;
        wrapper.append(label, select);
        return wrapper;
    }

    function addAssignmentRow() {
        const row = document.createElement('div');
        row.className = 'assignment-editor-row';
        const grid = document.createElement('div');
        grid.className = 'assignment-editor-grid';

        const branch = document.createElement('select');
        branch.className = 'form-select assignment-branch';
        branch.name = `assignments[${assignmentRowIndex}][branch_id]`;
        branch.required = true;
        appendOptions(branch, branches, 'name', 'Select branch');

        const course = document.createElement('select');
        course.className = 'form-select assignment-course';
        course.name = `assignments[${assignmentRowIndex}][course_id]`;
        course.required = true;
        course.disabled = true;
        appendOptions(course, [], 'course_name', 'Select course');

        const teacher = document.createElement('select');
        teacher.className = 'form-select assignment-teacher';
        teacher.name = `assignments[${assignmentRowIndex}][teacher_id]`;
        teacher.disabled = true;
        appendOptions(teacher, [], 'teacher_name', 'Optional teacher');

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'btn btn-outline-danger';
        remove.setAttribute('aria-label', 'Remove assignment row');
        remove.innerHTML = '<i class="bi bi-trash" aria-hidden="true"></i>';
        remove.addEventListener('click', () => row.remove());

        grid.append(makeField('Branch', branch), makeField('Course', course), makeField('Teacher', teacher), remove);
        row.appendChild(grid);
        newAssignments.appendChild(row);
        assignmentRowIndex += 1;
    }

    function renderCurrentAssignments(assignments) {
        currentAssignments.replaceChildren();
        document.getElementById('assignmentCount').textContent = assignments.length;

        if (assignments.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'assignment-empty';
            empty.textContent = 'No branch or course assignments yet.';
            currentAssignments.appendChild(empty);
            return;
        }

        assignments.forEach(assignment => {
            const card = document.createElement('div');
            card.className = 'assignment-card';
            card.dataset.assignmentId = assignment.id;

            [['Branch', assignment.branch_name], ['Course', assignment.course_name], ['Teacher', assignment.teacher_name || 'Not assigned']]
                .forEach(([labelText, valueText]) => {
                    const item = document.createElement('div');
                    const label = document.createElement('span');
                    label.className = 'assignment-card-label';
                    label.textContent = labelText;
                    const value = document.createElement('span');
                    value.className = 'assignment-card-value';
                    value.textContent = valueText || 'Not assigned';
                    item.append(label, value);
                    card.appendChild(item);
                });

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'btn btn-sm btn-outline-danger remove-assignment';
            remove.textContent = 'Remove';
            remove.setAttribute('aria-label', `Remove ${assignment.branch_name} assignment`);
            card.appendChild(remove);
            currentAssignments.appendChild(card);
        });
    }

    async function loadCurrentAssignments() {
        currentAssignments.innerHTML = '<div class="assignment-empty">Loading assignments...</div>';
        try {
            const url = assignmentsUrl.replace('__LESSON_ID__', activeLesson.id);
            const data = await readJson(await fetch(url, {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            }));
            renderCurrentAssignments(data.assignments || []);
        } catch (error) {
            currentAssignments.replaceChildren();
            showMessage(feedback, error.message, 'danger');
            console.error('Lesson assignments request failed:', error);
        }
    }

    function openLessonDetails(button) {
        activeLesson = { id: button.dataset.lessonId, title: button.dataset.lessonTitle };
        document.getElementById('modalLessonTitle').textContent = activeLesson.title;
        document.getElementById('modalLessonId').value = activeLesson.id;
        document.getElementById('editLessonLink').href = editLessonUrl.replace('__LESSON_ID__', activeLesson.id);
        feedback.replaceChildren();
        newAssignments.replaceChildren();
        assignmentRowIndex = 0;
        addAssignmentRow();
        modal.show();
        loadCurrentAssignments();
    }

    document.getElementById('addAssignmentButton').addEventListener('click', addAssignmentRow);

    newAssignments.addEventListener('change', async event => {
        const row = event.target.closest('.assignment-editor-row');
        if (!row) return;

        const branch = row.querySelector('.assignment-branch');
        const course = row.querySelector('.assignment-course');
        const teacher = row.querySelector('.assignment-teacher');

        if (event.target === branch) {
            const branchId = branch.value;
            course.disabled = true;
            teacher.disabled = true;
            appendOptions(course, [], 'course_name', branchId ? 'Loading courses...' : 'Select course');
            appendOptions(teacher, [], 'teacher_name', 'Optional teacher');
            if (!branchId) return;

            try {
                const response = await fetch(`/api/branches/${encodeURIComponent(branchId)}/courses`, {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' }
                });
                const courses = await readJson(response);
                if (branch.value !== branchId) return;
                appendOptions(course, courses, 'course_name', 'Select course');
                course.disabled = false;
            } catch (error) {
                appendOptions(course, [], 'course_name', 'Unable to load courses');
                showMessage(feedback, error.message, 'danger');
            }
        }

        if (event.target === course) {
            const branchId = branch.value;
            const courseId = course.value;
            teacher.disabled = true;
            appendOptions(teacher, [], 'teacher_name', courseId ? 'Loading teachers...' : 'Optional teacher');
            if (!branchId || !courseId) return;

            try {
                const url = `/api/branches/${encodeURIComponent(branchId)}/courses/${encodeURIComponent(courseId)}/teachers`;
                const response = await fetch(url, {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' }
                });
                const availableTeachers = await readJson(response);
                if (branch.value !== branchId || course.value !== courseId) return;
                appendOptions(teacher, availableTeachers, 'teacher_name', 'Optional teacher');
                teacher.disabled = false;
            } catch (error) {
                appendOptions(teacher, teachers, 'teacher_name', 'Optional teacher');
                teacher.disabled = false;
                showMessage(feedback, error.message, 'danger');
            }
        }
    });

    currentAssignments.addEventListener('click', async event => {
        const button = event.target.closest('.remove-assignment');
        if (!button) return;
        const card = button.closest('[data-assignment-id]');
        button.disabled = true;

        try {
            const url = deleteAssignmentUrl.replace('__ASSIGNMENT_ID__', card.dataset.assignmentId);
            await readJson(await fetch(url, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }));
            await loadCurrentAssignments();
            await refreshLessonList(currentPage);
        } catch (error) {
            button.disabled = false;
            showMessage(feedback, error.message, 'danger');
        }
    });

    assignmentForm.addEventListener('submit', async event => {
        event.preventDefault();
        const button = document.getElementById('saveAssignmentsButton');
        button.disabled = true;
        button.querySelector('.save-label').textContent = 'Saving...';
        button.querySelector('.spinner-border').classList.remove('d-none');

        try {
            const response = await fetch(assignmentForm.action, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(assignmentForm)
            });
            const data = await readJson(response);
            showMessage(feedback, data.message);
            newAssignments.replaceChildren();
            assignmentRowIndex = 0;
            addAssignmentRow();
            await loadCurrentAssignments();
            await refreshLessonList(currentPage);
        } catch (error) {
            showMessage(feedback, error.message, 'danger');
        } finally {
            button.disabled = false;
            button.querySelector('.save-label').textContent = 'Save assignments';
            button.querySelector('.spinner-border').classList.add('d-none');
        }
    });

    document.getElementById('deleteLessonButton').addEventListener('click', async event => {
        if (!activeLesson || !window.confirm(
            `Delete "${activeLesson.title}" and all of its related lesson data? This cannot be undone.`
        )) return;

        const button = event.currentTarget;
        button.disabled = true;
        try {
            const url = deleteLessonUrl.replace('__LESSON_ID__', activeLesson.id);
            const response = await fetch(url, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await readJson(response);
            modal.hide();
            await refreshLessonList(currentPage);
            showMessage(document.getElementById('lessonPageMessage'), data.message);
        } catch (error) {
            showMessage(feedback, error.message, 'danger');
        } finally {
            button.disabled = false;
        }
    });

    rowsContainer.addEventListener('click', event => {
        const button = event.target.closest('.lesson-details-button');
        if (button) openLessonDetails(button);
    });

    searchInput.addEventListener('input', () => {
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(() => refreshLessonList(1), 300);
    });

    paginationContainer.addEventListener('click', event => {
        const link = event.target.closest('a');
        if (!link) return;
        event.preventDefault();
        refreshLessonList(Number(new URL(link.href).searchParams.get('page') || 1));
    });

    window.addEventListener('popstate', () => {
        const params = new URLSearchParams(location.search);
        searchInput.value = params.get('search') || '';
        refreshLessonList(Number(params.get('page') || 1), false);
    });
})();
</script>
@endsection
