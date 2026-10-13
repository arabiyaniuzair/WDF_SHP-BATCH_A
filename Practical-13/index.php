
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 13 - Event CRUD</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <div class="container">
        <h1>Event Management</h1>
        

        <div id="message" role="status"></div>

        <form id="eventForm">
            <input type="hidden" id="event_id">

            <label for="event_name">Event Name</label>
            <input type="text" id="event_name" maxlength="150" required>

            <label for="event_date">Event Date</label>
            <input type="date" id="event_date" required>

            <label for="venue">Venue</label>
            <input type="text" id="venue" maxlength="150" required>

            <label for="description">Description</label>
            <textarea id="description" rows="3"></textarea>

            <button type="submit" id="saveButton">Add Event</button>
            <button type="button" id="cancelButton" hidden>Cancel Edit</button>
        </form>

        <h2>All Events</h2>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Venue</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="eventTable">
                    <tr><td colspan="6">Loading events...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
