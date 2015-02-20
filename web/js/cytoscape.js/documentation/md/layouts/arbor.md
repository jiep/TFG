The `arbor` layout uses a [force-directed](http://en.wikipedia.org/wiki/Force-directed_graph_drawing) physics simulation.  For more information about Arbor and its parameters, refer to [its documentation](http://arborjs.org/reference).

Notes about Arbor:

 * For webworkers to work properly, you need to point your browser to a server URL (e.g. `http://`) rather than a local address (e.g. `file://`).
 * Please note that the `liveUpdate` option can potentially be expensive, so if you are concerned about running time (e.g. for large graphs), you should set it to `false`.
 * Arbor does some automatic path finding because it uses web workers, meaning you have to directly include it in a `<script>` in your `<head>`.  Therefore, you can not combine `arbor.js` with your other JavaScript files &mdash; as you probably would as a part of the minification of the scripts in your webapp.
 * You probably want to use the version of `arbor.js` included with Cytoscape.js (or the unpatched, original [`arbor.js`](http://arborjs.org) if you are unaffected by the issues it contains).