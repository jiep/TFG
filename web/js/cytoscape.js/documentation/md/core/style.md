## Details

You can use this function to gain access to the visual style (stylesheet) after initialisation.  This is useful if you need to change the entire stylesheet at runtime.

Sets a new style by reference:

```js
// here a string stylesheet is used, but you could also use json or a cytoscape.Stylesheet object
var stringStylesheet = 'node { background-color: cyan; }';
cy.style( stringStylesheet );
```

Set an entirely new style to the graph, specifying [selectors](#selectors) and [style properties](#style) functionally:

```js
cy.style()
  .resetToDefault() // start a fresh default stylesheet

  // and then define new styles
  .selector('node')
  	.css('background-color', 'magenta')

  // ...

  .update() // update the elements in the graph with the new style
;
```

You can also add to the existing stylesheet:
```js
cy.style()
  .selector('node')
    .css({
      'background-color': 'yellow'
    })

  .update() // update the elements in the graph with the new style
;
```

You can also set the style from plain JSON:

```js
cy.style()
  .fromJson([
    {
      selector: 'node',
      css: {
        'background-color': 'red'
      }
    }

    // , ...
  ])

  .update() // update the elements in the graph with the new style
;
```

You can also set the style from a style string (that you would probably pull from a file on your server):

```js
cy.style()
  .fromString('node { background-color: blue; }')

  .update() // update the elements in the graph with the new style
;
```