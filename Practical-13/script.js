
const apiUrl = "api.php";

const form = document.getElementById("eventForm");
const eventTable = document.getElementById("eventTable");
const message = document.getElementById("message");
const saveButton = document.getElementById("saveButton");
const cancelButton = document.getElementById("cancelButton");

async function requestApi(url, options = {}) {
    const response = await fetch(url, options);
    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(result.message || "Request failed.");
    }

    return result;
}

function showMessage(text, success = true) {
    message.textContent = text;
    message.className = success ? "success" : "error";
}

async function loadEvents() {
    try {
        const result = await requestApi(apiUrl);
        eventTable.replaceChildren();

        if (result.data.length === 0) {
            const row = document.createElement("tr");
            const cell = document.createElement("td");
            cell.colSpan = 6;
            cell.textContent = "No events found.";
            row.appendChild(cell);
            eventTable.appendChild(row);
            return;
        }

        result.data.forEach(event => {
            const row = document.createElement("tr");

            [
                event.event_id,
                event.event_name,
                event.event_date,
                event.venue,
                event.description || ""
            ].forEach(value => {
                const cell = document.createElement("td");
                cell.textContent = value;
                row.appendChild(cell);
            });

            const actions = document.createElement("td");

            const editButton = document.createElement("button");
            editButton.textContent = "Edit";
            editButton.type = "button";
            editButton.className = "edit";
            editButton.addEventListener("click", () => editEvent(event));

            const deleteButton = document.createElement("button");
            deleteButton.textContent = "Delete";
            deleteButton.type = "button";
            deleteButton.className = "delete";
            deleteButton.addEventListener("click", () => deleteEvent(event.event_id));

            actions.append(editButton, deleteButton);
            row.appendChild(actions);
            eventTable.appendChild(row);
        });
    } catch (error) {
        showMessage(error.message, false);
    }
}

form.addEventListener("submit", async function (event) {
    event.preventDefault();

    const id = document.getElementById("event_id").value;

    const data = {
        action: id ? "update" : "add",
        event_id: id,
        event_name: document.getElementById("event_name").value.trim(),
        event_date: document.getElementById("event_date").value,
        venue: document.getElementById("venue").value.trim(),
        description: document.getElementById("description").value.trim()
    };

    try {
        const result = await requestApi(apiUrl, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        });

        showMessage(result.message);
        resetForm();
        await loadEvents();
    } catch (error) {
        showMessage(error.message, false);
    }
});

function editEvent(event) {
    document.getElementById("event_id").value = event.event_id;
    document.getElementById("event_name").value = event.event_name;
    document.getElementById("event_date").value = event.event_date;
    document.getElementById("venue").value = event.venue;
    document.getElementById("description").value = event.description || "";

    saveButton.textContent = "Update Event";
    cancelButton.hidden = false;
    form.scrollIntoView({ behavior: "smooth" });
}

async function deleteEvent(id) {
    if (!confirm("Are you sure you want to delete this event?")) {
        return;
    }

    try {
        const result = await requestApi(apiUrl, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                action: "delete",
                event_id: id
            })
        });

        showMessage(result.message);
        await loadEvents();
    } catch (error) {
        showMessage(error.message, false);
    }
}

function resetForm() {
    form.reset();
    document.getElementById("event_id").value = "";
    saveButton.textContent = "Add Event";
    cancelButton.hidden = true;
}

cancelButton.addEventListener("click", resetForm);

loadEvents();
