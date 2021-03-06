## Goals

By performing the tests described below, I hoped to observe relationsips between the number of fields on a Drupal content type and the time-to-completion for a set of tasks (such as rendering node conent, add/edit forms, REST requests, etc.) on nodes of that content type. My motiviation for doing this was to 1) to document practical limits on the number of fields on a content type, and 2) to provide data that would generate additional areas of investigation leading to workarounds or strategies for managing Islandora content types that have large numbers of metadata fields.

## Methodology

### Environment

All tests were done on an [Islandora Playbook](https://github.com/Islandora-Devops/islandora-playbook) virutal machine, using its master branch at commit 47e829a2b222ebcb5c3f6e537c79d107912b40f9 (March 29, 2020, a few of weeks prior to the release of Islandora 8 1.1.0). This VM used the default Islandora Playbook settings (1 CPU, 4GB of RAM, Ubuntu 16.04, MySQL as the backend database). The host machine was a Thinkpad i5-8350U CPU @ 1.70GHz × 8 with 16GB of RAM running Ubuntu 18.04.

### Data collection

To generate the data, I created a Drupal module using the `drupal_field_limit_tester.php` script with a `$num_csv_records` value of 1 (to generate one sample node). After enabling the module, I performed the following tasks:

* Migrated 1 node from CSV
* Viewed (using Chrome) this node as anonymous, with an empty Drupal cache
* Viewed (using Chrome) this node as anonymous with a populated Drupal cache
   * Fetched this same content using curl with both empty and populated Drupal cache
* Viewed (using Chrome) the node add form for my content type, as the "admin" user
* Viewed (using Chrome) the node edit form for my content type (populated with node content), as the "admin" user
* Retrieved without rendering (using Chrome) the node edit form for my content type (populated with node content), as the "admin" user
* Using curl, issued a `GET` request to the sample node's JSON endpoint with an empty Drupal cache, authenticated as the "admin" user
* Using curl, issued a `GET` request to the sample node's JSON endpoint with a populated Drupal cache, authenticated as the "admin" user
* Using curl, issued a `POST` request to create a node
* Using curl, issued a `PATCH` request to update a single field on a node

Performing these tasks provided sufficient time-to-completion data to identify aspects of page rendering that define the practical maximum number of fields on a content type.

To time the tasks performed using Chrome, I used its "Performance" tool, available in the hamburger menu > More tools > Developer tools > Peformance. The data I recorded was the "Total" value listed in the tool's "Summary" output. For one set of data points (see "Rendering the node add and edit forms" for details), I used Chrome's "Network" tool (hamburger menu > More tools > Developer tools > Network. To time the tasks performed using curl, I ran the requests in conjuction with the Linux `time` command, e.g., `time curl http://localhost:8000/node/50` and used the "Real" value from this ouput.

I then rolled back the migration and uninstalled the module, repeating the entire set of tasks for nodes with with 100, 150, 200, 250, 300, 350, 400, 450, and 500 fields.

## Results

### Overall

The data collected from these tasks is available in [this CSV file](results.csv). A chart plotting the number of fields along the X axis (100 to 500 in increments of 50) against the time required to complete the tasks along the Y axis (0 to 50 seconds) looks like this:

!['Chart showing all test results'](chart-all-results.png)

Below, I will break out some of the specific results.

### Rendering the node add and edit forms

The biggest impact of increasing number of fields on a content type is the time it takes for the node add and edit forms to finish rendering:

!['Chart showing test results for REST requests'](chart-forms.png)

At 200 fields, it takes over 5 seconds to render both the blank node add form and a populated node edit form. At 300 fields, that time is over twice as long (over 10 seconds). At 500 fields, it takes over 40 seconds to finish rendering a populated node edit form. Clearly, the UX for content editors diminshes as the number of fields increases.

The most likely cause of these long rendering times is the JavaScript used by the node add and edit forms. Chrome's performance tool helpfully breaks down the time to render a page into loading, scripting, rendering, painting, system, and idle slices. By far, the largest slice of activity when viewing the node add and edit forms is scripting, followed by rendering. Here is a representative visualization provided by the tool:

!['Pie chart showing scripting and rendering time'](node_edit_form_summary.png)

During very long rendering of forms, I observed some unusual behavior in the drag-and-drop UI elements provided by Drupal to order multiple field values:

!['Screenshot of multivalued field with drag and drop'](node_edit_form_drag_and_drop.png)

Specifically, the drag-and-drop UI elements, which use JavaScript under the hood, did not render as expected. Instead, at the start of the form rendering process, Drupal temporarily reverted to the native HTML "row weight" form elements:

!['Screenshot of multivalued field with row widgets'](node_edit_form_with_row_widgets.png)

The expected drag-and-drop UI elements eventually appeared near the end of the form rendering process.

This behavior suggested that the JavaScript used to render the drag-and-drop UI elements was implicated in the very long scripting and rendering times shown in the donut chart above. To confirm this, I dug deeper in Chrome's Performance tool, which revealed that that the main JavaScript library loaded by the node edit form took approximately 30 seconds to execute of the 43 seconds required to render the edit form.

To isolate the impact of executing JavaScript when rendering the node edit form, I used Chrome's "Network" tool (hamburger menu > More tools > Developer tools > Network) to time how long Chrome spends retrieving *but not rendering* the popluated node edit form from Drupal. The values shown in red in the following chart are the "Finish" values provided by the Network tool, which represent the sum of the retrieval times for all JavaScript, CSS, image, etc. files referenced in the node edit form; the values in blue show the amount of time Chrome takes to render the node edit form (retrieve the form markup and data from Drupal, execute all required JavaScript, then actually render the form):

!['Chart showing rendering of the GUI node edit form vs. retieving markup and data only'](node_edit_form_render_vs_download.png)

The short times to retrieve the page content compared to the long times required to render the same content confirm that the impact of executing JavaScript and rendering the page is substantial. A more specific conclusion we can make is that the overall time consumed rendering the node edit form has a more or less linear relationship to the number of fields in the edit form. The same can be said of the node add form.

### Viewing node content

This chart shows the time required to render node content in Chrome and to download it using curl:

!['Node view test results'](node-view.png)

Based on this data, we see that requests for uncached nodes takes substantially longer than requests for cached nodes, for both graphical and non-graphical clients. I expected Chrome to take longer to render cached node content (the red line in the chart) as the number of fields increased, but the data shows that is not the case.

Drupal's page caching for anonymous users is very effective, so it isn't surprising that the number of fields on a node did not increase the amount of time required to render or download the cached node content and markup. The time required to retrieve uncached node content and markup did increase with the number of fields on a node, using both Chrome and curl, but the increase was greater in Chrome than using curl, which is to be expected since it needs to render the additional fields. 

### REST requests

Number of fields didn't have an appreciable impact on any of the tested REST requests:

!['Chart showing all test results'](chart-rest.png)

Requesting the JSON representation of an node (via `GET`) is fairly fast regardless of the number of fields, especially requests for cached content, and even for authenticated users. It isn't surprising that requesting the JSON via a REST request is faster than fetching a fully rendered version of the node, since the JSON representation contains less node data and no HTML markup.

At 500 fields, retrieving a node's uncached JSON representation (via `GET`) and adding nodes (via `POST`) started to take a bit longer than with fewer fields, but updating a single field via `PATCH` was consistently quick all the way up to 500 fields.

## Limitations

This exploration of the practical number of fields you can attach to a Drupal content type provides some baseline data up to 500 fields. However, it has the following limitiations:

* It only tested the time it takes using a graphical web browser to *render* node add and edit forms. Chrome's developer tools do not provide a way (as far as I can tell) of timing form submit operations.
* All fields attached to nodes for testing purposes are simple text fields (i.e., this study doesn't test for performance implications of other field types such as taxonomy fields).
* While this study does control for server-side caching (in Drupal at least), it does not account for caching done by Chrome.

## Conclusions

Based on the data presented here, the largest impact of large numbers of fields attached to a node is the UX for content editors: the more fields, the longer it takes to render (and therefore use) node add and edit forms. There may be ways to mitigate this, for example by breaking up add/edit forms into multiple smaller forms using something like the [Forms Steps](https://www.drupal.org/project/forms_steps) contrib module.

Lage numbers of fields did not have a substantial impact on the time required to view a node (at least cached versions of nodes), or on REST operations, including create (`POST`) and update (`PATCH`) requests. The efficiency of REST requests suggests that it might be useful to investigate using decoupled Drupal clients to replace Drupal's rendered HTML add/edit forms in some applications, provided the UX of those clients doesn't also suffer when dealing with nodes that contain very large numbers of fields.

