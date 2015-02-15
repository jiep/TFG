;(function($$){ 'use strict';

  var CanvasRenderer = $$('renderer', 'canvas');

  // Draw edge text
  CanvasRenderer.prototype.drawEdgeText = function(context, edge) {
    var text = edge._private.style['content'].strValue;

    if( !text || text.match(/^\s+$/) ){
      return;
    }

    if( this.hideEdgesOnViewport && (this.dragData.didDrag || this.pinching || this.hoverData.dragging || this.data.wheel || this.swipePanning) ){ return; } // save cycles on pinching

    var computedSize = edge._private.style['font-size'].pxValue * edge.cy().zoom();
    var minSize = edge._private.style['min-zoomed-font-size'].pxValue;

    if( computedSize < minSize ){
      return;
    }
  
    // Calculate text draw position
    
    context.textAlign = 'center';
    context.textBaseline = 'middle';
    
    // this.recalculateEdgeLabelProjection( edge );
    
    var rs = edge._private.rscratch;
    this.drawText(context, edge, rs.labelX, rs.labelY);
  };

  // Draw node text
  CanvasRenderer.prototype.drawNodeText = function(context, node) {
    var text = node._private.style['content'].strValue;

    if ( !text || text.match(/^\s+$/) ) {
      return;
    }

    var computedSize = node._private.style['font-size'].pxValue * node.cy().zoom();
    var minSize = node._private.style['min-zoomed-font-size'].pxValue;

    if( computedSize < minSize ){
      return;
    }
      
    // this.recalculateNodeLabelProjection( node );

    var textHalign = node._private.style['text-halign'].strValue;
    var textValign = node._private.style['text-valign'].strValue;
    var rs = node._private.rscratch;

    switch( textHalign ){
      case 'left':
        context.textAlign = 'right';
        break;

      case 'right':
        context.textAlign = 'left';
        break;

      default: // e.g. center
        context.textAlign = 'center';
    }

    switch( textValign ){
      case 'top':
        context.textBaseline = 'bottom';
        break;

      case 'bottom':
        context.textBaseline = 'top';
        break;

      default: // e.g. center
        context.textBaseline = 'middle';
    }

    this.drawText(context, node, rs.labelX, rs.labelY);
  };
  
  CanvasRenderer.prototype.getFontCache = function(context){
    var cache;

    this.fontCaches = this.fontCaches || [];

    for( var i = 0; i < this.fontCaches.length; i++ ){
      cache = this.fontCaches[i];

      if( cache.context === context ){
        return cache;
      }
    }

    cache = {
      context: context
    };
    this.fontCaches.push(cache);

    return cache;
  };

  // set up canvas context with font
  // returns transformed text string
  CanvasRenderer.prototype.setupTextStyle = function( context, element ){
    // Font style
    var parentOpacity = element.effectiveOpacity();
    var style = element._private.style;
    var labelStyle = style['font-style'].strValue;
    var labelSize = style['font-size'].pxValue + 'px';
    var labelFamily = style['font-family'].strValue;
    var labelWeight = style['font-weight'].strValue;
    var opacity = style['text-opacity'].value * style['opacity'].value * parentOpacity;
    var outlineOpacity = style['text-outline-opacity'].value * opacity;
    var color = style['color'].value;
    var outlineColor = style['text-outline-color'].value;

    var fontCacheKey = element._private.fontKey;
    var cache = this.getFontCache(context);

    if( cache.key !== fontCacheKey ){
      context.font = labelStyle + ' ' + labelWeight + ' ' + labelSize + ' ' + labelFamily;

      cache.key = fontCacheKey;
    }

    var text = String(style['content'].value);
    var textTransform = style['text-transform'].value;
    
    if (textTransform == 'none') {
    } else if (textTransform == 'uppercase') {
      text = text.toUpperCase();
    } else if (textTransform == 'lowercase') {
      text = text.toLowerCase();
    }
    
    // Calculate text draw position based on text alignment
    
    // so text outlines aren't jagged
    context.lineJoin = 'round';

    this.fillStyle(context, color[0], color[1], color[2], opacity);
    
    this.strokeStyle(context, outlineColor[0], outlineColor[1], outlineColor[2], outlineOpacity);

    return text;
  };

  // Draw text
  CanvasRenderer.prototype.drawText = function(context, element, textX, textY) {
    var style = element._private.style;
    var parentOpacity = element.effectiveOpacity();
    if( parentOpacity === 0 ){ return; }

    var text = this.setupTextStyle( context, element );
    
    if ( text != null && !isNaN(textX) && !isNaN(textY) ) {
     
      var lineWidth = 2  * style['text-outline-width'].value; // *2 b/c the stroke is drawn centred on the middle
      if (lineWidth > 0) {
        context.lineWidth = lineWidth;
        context.strokeText(text, textX, textY);
      }

      context.fillText(text, textX, textY);
    }
  };

  
})( cytoscape );