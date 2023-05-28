Backend
    Currently the url hashing system supports around 47 crores records. We may expand the number to support url
    In the current db schema to store all the url in the only one table, In future We will move the unsused urls to the new table named as "archived_urls". It will wait make search faster in the table.
    We will update the active column value for the url which is not currently active and it will optimized the searching in the table.
    We can use the sentry error notification for notifying the developers or we can notify the developer via mail when there is error is occured.
    we can also use the kibana for tracking the query which takes more time to perform.

Frontend
    Add a simple UI for the url hashing system.
    Supports for redirection from tiny url to original url.
