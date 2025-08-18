import React from 'react'; // Make sure to import React

// A mapping from HTML tag names to React component types.
// Using 'strong' for 'b' and 'em' for 'i' is semantically better.
const tagMap = {
  p: 'p',
  b: 'strong',
  i: 'em',
  ul: 'ul',
  li: 'li',
  span: 'span',
  h3: 'h3'
  // Add other simple tags here if needed
};

/**
 * A recursive function that parses an HTML string into an array of
 * React nodes (elements and text strings).
 */
const parseHtml = (html) => {
  const elements = [];
  let i = 0;

  while (i < html.length) {
    const openTagStart = html.indexOf('<', i);

    // --- 1. Handle Text Before a Tag ---
    // If there's text before the next tag (or no tags left), add it as a string.
    const text = html.slice(i, openTagStart === -1 ? html.length : openTagStart);
    if (text) {
      elements.push(text);
    }
    
    // If no tags are left, we're done.
    if (openTagStart === -1) break;

    // --- 2. Handle The HTML Tag ---
    const openTagEnd = html.indexOf('>', openTagStart);
    if (openTagEnd === -1) break; // Malformed tag

    const tagName = html.slice(openTagStart + 1, openTagEnd);
    const closingTag = `</${tagName}>`;
    
    // Find the corresponding closing tag, accounting for nested tags of the same type.
    let tagCount = 1;
    let closingTagIndex = -1;
    let searchPos = openTagEnd + 1;
    
    while (tagCount > 0) {
      const nextClosing = html.indexOf(closingTag, searchPos);
      const nextOpening = html.indexOf(`<${tagName}>`, searchPos);

      if (nextClosing === -1) break; // No closing tag found

      // If a nested opening tag of the same type appears first, increment count.
      if (nextOpening !== -1 && nextOpening < nextClosing) {
        tagCount++;
        searchPos = nextOpening + 1;
      } else {
      // Otherwise, we found a closing tag for a level.
        tagCount--;
        searchPos = nextClosing + 1;
        if (tagCount === 0) {
          closingTagIndex = nextClosing;
        }
      }
    }
    
    if (closingTagIndex === -1) {
      // Malformed HTML, couldn't find a match.
      i = openTagEnd + 1;
      continue;
    }

    // --- 3. RECURSION ---
    // Get the content between the tags.
    const innerHtml = html.slice(openTagEnd + 1, closingTagIndex);
    // Recursively parse the inner content to get the children!
    const children = parseHtml(innerHtml);

    // Create the React element.
    const elementType = tagMap[tagName] || 'span'; // Default to <span> for unknown tags
    elements.push(React.createElement(elementType, { key: i }, children));
    
    // Move index past the element we just parsed.
    i = closingTagIndex + closingTag.length;
  }

  return elements;
};


const HTMLParser = ({ content }) => {
  if (!content) {
    return null;
  }
  
  return <div>{parseHtml(content)}</div>;
};

export default HTMLParser;