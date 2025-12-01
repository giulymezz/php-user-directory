<h1 class="page-title">USERS</h1>

<div class="form-wrapper">
    <form method="post" class="filter-form" id="user-form">

        <label for="active">Active:</label>
        <select name="active" id="active">
            <option value="">All</option>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>

        <label for="from">From:</label>
        <input type="text" name="from" id="from" placeholder="d/m/Y H:i:s">

        <label for="to">To:</label>
        <input type="text" name="to" id="to" placeholder="d/m/Y H:i:s">

        <label for="name">Name:</label>
        <input type="text" name="name" id="name" placeholder="starts with...">

        <label for="surname">Surname:</label>
        <input type="text" name="surname" id="surname" placeholder="starts with...">

        <label for="view">View:</label>
        <select name="view" id="view">
            <option value="table">Table</option>
            <option value="thumb">Thumb</option>
        </select>

        <button type="submit" class="btn">Search</button>

    </form>
</div>