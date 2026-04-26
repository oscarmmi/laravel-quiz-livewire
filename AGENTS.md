# AGENTS.md - AI Coding Assistant Guidelines

This file provides guidelines for the AI coding assistant to work effectively on the **Laravel Quiz System** project.

## 1. Communication Rules

### Before Responding
- **Always read relevant files first** before making any changes
- If unclear about the goal, **ask for clarification**
- Do NOT assume what the user wants - verify first

### When User Sends Code/Error
- Read the specific files mentioned
- Explain what the code does in 1-2 sentences
- Ask what they want to achieve before making changes

### After Making Changes
- Verify the change works by checking related files
- Clear cache when needed: `php artisan view:clear`

## 2. Project-Specific Workflow

### Step 1: Understand Context
```
1. Read .cursorrules for project rules
2. Look at relevant files (components, models, routes)
3. Check how similar features are implemented elsewhere
```

### Step 2: Implement Changes
```
1. Make minimal, targeted changes
2. Follow existing code patterns in the project
3. Use existing components (e.g., <x-modal>, <x-primary-button>)
```

### Step 3: Verify
```
1. Check for syntax errors
2. Ensure single root element for Livewire components
3. Run php artisan test if applicable
```

## 3. Tech Stack Reference

| Technology | Usage |
|-----------|-------|
| Laravel 13 | Backend framework |
| Livewire 3 | Dynamic UI components |
| Alpine.js | Client-side interactivity |
| Tailwind CSS | Styling |
| Volt | Anonymous Livewire components |
| Eloquent | Database ORM |

## 4. Important Conventions

### Livewire Components
- **Single root element** - Always wrap in one `<div>` to avoid MultipleRootElementsDetectedException
- Use `$dispatch('open-modal', 'name')` to open modals
- Use `$dispatch('close')` to close modals

### Database Queries
- Use `with('relation')` for eager loading
- Follow .cursorrules guidelines

### Frontend
- Use existing Blade components: `<x-modal>`, `<x-primary-button>`, `<x-secondary-button>`
- Use Tailwind utility classes (no custom CSS)

## 5. Common Pitfalls to Avoid

1. **Multiple root elements** - Always use ONE wrapper div
2. **Vanilla JS instead of Alpine** - Use Alpine for interactivity
3. **Assume data** - Always verify what data is available
4. **Skip reading files** - Read first, then act

## 6. Testing Changes

Before running tests:
```bash
php artisan view:clear
php artisan test
```

---

**Remember:** It is better to ask for clarification than to make wrong assumptions.