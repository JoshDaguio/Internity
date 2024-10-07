<div class="modal fade" id="composeMessageModal" tabindex="-1" role="dialog" aria-labelledby="composeMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="composeMessageModalLabel">Compose Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('messages.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="role" class="form-label">Select Role</label>
                        <select id="role" name="role" class="form-select">
                            <option value="" selected disabled>Select Role</option>
                            <option value="admins">Admins</option>
                            <option value="3">Faculty</option>
                            <option value="4">Company</option>
                            <option value="5">Student</option>
                        </select>
                    </div>
                    <div class="mb-3" id="course-container" style="display: none;">
                        <label for="course" class="form-label">Select Course</label>
                        <select id="course" class="form-select">
                            <option value="" selected disabled>Select Course</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="to" class="form-label">To</label>
                        <select id="to" name="recipient_id" class="form-select">
                            <option value="" selected disabled>Select Recipient</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="body" class="form-label">Message</label>
                        <textarea class="form-control" id="body" name="body" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role');
        const toSelect = document.getElementById('to');
        const courseSelect = document.getElementById('course');
        const courseContainer = document.getElementById('course-container');
        const userRole = '{{ Auth::user()->role_id }}';

        // Hide options based on the user's role
        if (userRole === '3') { // Faculty
            roleSelect.querySelector('option[value="3"]').style.display = 'none';
            roleSelect.querySelector('option[value="4"]').style.display = 'none';
        } else if (userRole === '5') { // Student
            roleSelect.querySelector('option[value="5"]').style.display = 'none';
        } else if (userRole === '4') { // Company
            roleSelect.querySelector('option[value="3"]').style.display = 'none';
            roleSelect.querySelector('option[value="4"]').style.display = 'none';
        }

        roleSelect.addEventListener('change', function () {
            const selectedRole = this.value;
            toSelect.innerHTML = '<option value="" selected disabled>Loading...</option>';

            // Show course dropdown if role is Faculty (3) or Student (5) and the current user is Admin (2) or Super Admin (1)
            if ((selectedRole === '3' || selectedRole === '5') && ['1', '2'].includes(userRole)) {
                fetch(`{{ route('courses.index') }}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    courseSelect.innerHTML = '<option value="" selected disabled>Select Course</option>';
                    data.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course.id;
                        option.text = course.course_code;
                        courseSelect.appendChild(option);
                    });
                    courseContainer.style.display = 'block'; // Show the course dropdown

                    // Add event listener to course select dropdown
                    courseSelect.addEventListener('change', function () {
                        const courseId = this.value;
                        fetch(`/messages/recipients/${selectedRole}?course=${courseId}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(response => response.json())
                        .then(data => {
                            populateRecipients(data);
                        });
                    });
                });
            } else {
                courseContainer.style.display = 'none';
                fetch(`/messages/recipients/${selectedRole}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    populateRecipients(data);
                });
            }
        });

        function populateRecipients(data) {
            toSelect.innerHTML = '<option value="" selected disabled>Select Recipient</option>';
            if (data.length === 0) {
                toSelect.innerHTML = '<option value="" disabled>No recipients found</option>';
            } else {
                data.forEach(function (recipient) {
                    const option = document.createElement('option');
                    option.value = recipient.id;
                    option.text = recipient.name;
                    toSelect.appendChild(option);
                });
            }
        }
    });
</script>
