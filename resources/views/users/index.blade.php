<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Form with DataTable</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .error-message { color: red; font-size: 0.9em; }
    </style>
</head>
<body>

<div class="container mt-4">
    <h2>User Registration Form</h2>
    <form id="userForm" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" >
            <div id="nameError" class="error-message"></div>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email">
            <div id="emailError" class="error-message"></div>
        </div>

        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" class="form-control" id="phone" name="phone">
            <div id="phoneError" class="error-message"></div>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description"></textarea>
            <div id="descriptionError" class="error-message"></div>
        </div>

        <div class="form-group">
            <label for="role_id">Role:</label>
            <select class="form-control" id="role_id" name="role_id">
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
            <div id="role_idError" class="error-message"></div>
        </div>

        <div class="form-group">
            <label for="profile_image">Profile Image:</label>
            <input type="file" class="form-control" id="profile_image" name="profile_image">
            <div id="profile_imageError" class="error-message"></div>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <h2 class="mt-5">Users Table</h2>
    <table id="userTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Description</th>
                <th>Role</th>
                <th>Profile Image</th>
            </tr>
        </thead>
        <tbody>
            <!-- User data will be dynamically loaded here -->
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script>
    const BASE_URL = 'http://localhost/testProject';

    $(document).ready(function () {
       
        const userTable = $('#userTable').DataTable({
            ajax: {
                url: `{{url('/')}}/api/users`,
                dataSrc: 'data'
            },
            columns: [
                { data: 'name' },
                { data: 'email' },
                { data: 'phone' },
                { data: 'description' },
                { data: 'role.name' },
                { data: 'profile_image', render: function(data) {
                    return data ? `<img src="{{url('/')}}/${data}" alt="Profile Image" width="50">` : '';
                }}
            ]
        });


        $('#userForm').on('submit', function (e) {
            e.preventDefault();
            $('.error-message').empty();

            const formData = new FormData(this);
            let isValid = true;

            const name = $('#name').val();
            if (!name || name.length > 255) {
                $('#nameError').text('Name is required and must not exceed 255 characters.');
                isValid = false;
            }
           
            const email = $('#email').val();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email || !emailPattern.test(email)) {
                $('#emailError').text('Please enter a valid email address.');
                isValid = false;
            }

            const phone = $('#phone').val();
            const phonePattern = /^[6-9]\d{9}$/;
            if (!phone || !phonePattern.test(phone)) {
                $('#phoneError').text('Phone number is required and must be a valid 10-digit Indian number.');
                isValid = false;
            }

            const roleId = $('#role_id').val();
            if (!roleId) {
                $('#role_idError').text('Please select a role.');
                isValid = false;
            }

            const profileImage = $('#profile_image')[0].files[0];
            if (profileImage) {
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                const maxSize = 2 * 1024 * 1024; // 2MB

                if (!allowedTypes.includes(profileImage.type)) {
                    $('#profile_imageError').text('Profile image must be a JPEG, JPG, or PNG file.');
                    isValid = false;
                } else if (profileImage.size > maxSize) {
                    $('#profile_imageError').text('Profile image must not exceed 2MB.');
                    isValid = false;
                }
            }

            const description = $('#description').val();
            if (description && typeof description !== 'string') {
                $('#descriptionError').text('Description must be a valid text.');
                isValid = false;
            }

            if (isValid) {
                $.ajax({
                    url: `{{url('/')}}/api/users`,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function () {
                        $('#userForm')[0].reset(); 
                        userTable.ajax.reload();
                    },
                    error: function (xhr) {
                        const errors = xhr.responseJSON.errors;
                        for (const [key, value] of Object.entries(errors)) {
                            $(`#${key}Error`).text(value.join(', '));
                        }

                        const oldData = xhr.responseJSON.old || {};
                        $('#name').val(oldData.name || '');
                        $('#email').val(oldData.email || '');
                        $('#phone').val(oldData.phone || '');
                        $('#description').val(oldData.description || '');
                        $('#role_id').val(oldData.role_id || '');
                    }
                });
            }
        });
    });
</script>

</body>
</html>
