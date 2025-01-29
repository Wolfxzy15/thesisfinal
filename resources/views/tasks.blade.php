<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Task Manager</title>
    <link href="/css/css.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <nav>
        <span>Task Manager</span>
    </nav>

    <div id="add">
        <form action="/tasks" method="post">
            @csrf
            <h2>Add Task</h2>
            <input type="text" placeholder="Task Name" name="name" class="form-control">
            <textarea name="description" placeholder="Description" class="form-control"></textarea>
            <input type="date" placeholder="Deadline" name="deadline" class="form-control">
            <button type="submit" id="submit">Submit</button>
        </form>
    </div>

    <div id="display">
        <table>
            <tr>
                <th>Completed</th>
                <th>Name</th>
                <th>Description</th>
                <th>Deadline</th>
                <th>Delete</th>
            </tr>
            @foreach ($tasks as $task)
            <tr>
                <td>
                    <form action="/toggle-completed/{{ $task['name'] }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="checkbox" name="completed" {{ isset($task['completed']) && $task['completed'] ? 'checked' : '' }} onclick="this.form.submit()">
                    </form>
                </td>
                <td>{{$task['name']}}</td>
                <td>{{$task['description']}}</td>
                <td>{{$task['deadline']}}</td>
                <td>
                    <form action="/delete/{{$task['name']}}" method="post">
                        @csrf
                        @method('DELETE')
                        <button>Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</body>
</html>
