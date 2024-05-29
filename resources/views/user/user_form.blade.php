<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="container mt-5">
    <h2>User Form</h2>
    <div id="alert" class="alert d-none"></div>
    <form id="userForm">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description"></textarea>
        </div>
        <div class="form-group">
            <label for="role_id">Role</label>
            <select class="form-control" id="role_id" name="role_id" required>
                @foreach(App\Models\Role::all() as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="profile_image">Profile Image</label>
            <input type="file" class="form-control" id="profile_image" name="profile_image">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <h3 class="mt-5">User List</h3>
    <table class="table">
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
        <tbody id="userTableBody">
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function () {
        fetchUsers();

        $('#userForm').on('submit', function (e) {
            e.preventDefault();

            var formData = new FormData(this);
            $.ajax({
                url: '/api/users',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#alert').removeClass('d-none alert-danger').addClass('alert-success').text('User created successfully.');
                    fetchUsers();
                    $('#userForm')[0].reset();
                },
                error: function (response) {
                    let errors = response.responseJSON.errors;
                    let errorHtml = '<ul>';
                    for (let key in errors) {
                        errorHtml += '<li>' + errors[key][0] + '</li>';
                    }
                    errorHtml += '</ul>';
                    $('#alert').removeClass('d-none alert-success').addClass('alert-danger').html(errorHtml);
                }
            });
        });

        function fetchUsers() {
            $.ajax({
                url: '/api/users',
                type: 'GET',
                success: function (response) {
                    let users = response.users;
                    let userTableBody = $('#userTableBody');
                    userTableBody.empty();
                    users.forEach(user => {
                        userTableBody.append(`
                            <tr>
                                <td>${user.name}</td>
                                <td>${user.email}</td>
                                <td>${user.phone}</td>
                                <td>${user.description}</td>
                                <td>${user.role.name}</td>
                                <td><img src="/${user.profile_image}" width="50"></td>
                            </tr>
                        `);
                    });
                }
            });
        }
    });
</script>
</body>
</html>