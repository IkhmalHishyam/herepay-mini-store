import DOMPurify from 'dompurify'

/**
 * Sanitizes HTML content to prevent XSS attacks while preserving safe formatting
 */
export function sanitizeHtml(html: string): string {
    if (!html) return ''
    
    // Configure DOMPurify to allow common formatting elements
    const config = {
        // Allow these tags
        ALLOWED_TAGS: [
            'p', 'br', 'strong', 'em', 'u', 'b', 'i',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'ul', 'ol', 'li',
            'a', 'blockquote',
            'table', 'thead', 'tbody', 'tr', 'td', 'th',
            'code', 'pre'
        ],
        
        // Allow these attributes
        ALLOWED_ATTR: [
            'href', 'target', 'rel', 'title'
        ],
        
        // Allow these URI schemes
        ALLOWED_URI_REGEXP: /^(?:(?:(?:f|ht)tps?|mailto|tel|callto|sms|cid|xmpp):|[^a-z]|[a-z+.\-]+(?:[^a-z+.\-:]|$))/i,
        
        // Ensure links open in new tab and have security attributes
        FORBID_ATTR: ['style', 'class'],
        
        // Keep content safe
        KEEP_CONTENT: true,
        
        // Transform links to be safe
        ADD_ATTR: {
            'a': {
                'target': '_blank',
                'rel': 'noopener noreferrer'
            }
        }
    }
    
    return DOMPurify.sanitize(html, config)
}

/**
 * Sanitizes HTML content for display in product cards (more restrictive)
 */
export function sanitizeHtmlForCard(html: string): string {
    if (!html) return ''
    
    // More restrictive config for product cards
    const config = {
        ALLOWED_TAGS: [
            'p', 'br', 'strong', 'em', 'b', 'i',
            'ul', 'ol', 'li'
        ],
        ALLOWED_ATTR: [],
        KEEP_CONTENT: true,
        FORBID_TAGS: ['a', 'img', 'script', 'style']
    }
    
    return DOMPurify.sanitize(html, config)
}