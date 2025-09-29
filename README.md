# Project Setup Guide (React + TypeScript + Vite)

## Versions

- **Node:** 22.13.0
- **React:** 18.3.1
- **TypeScript:** 5.6.2

## Required Variables for Local Development

To run the project locally, make sure to define the following environment variable:

- `REACT_APP_BASE_URL`

## Creating Components

To ensure standardized and maintainable code, adhere to the following guidelines when creating components:

### General Rules

1. **React Arrow Functional Components Only:**
   Always use arrow functions for React components.

2. **Folder Naming Convention:**
   Use `kebab-case` for folder names.

3. **Component File Naming Convention:**
   Create components with `PascalCase` naming, using the `.tsx` extension.

4. **Static Constants Naming Convention:**  
   Use `SNAKE_CASE` (uppercase with underscores) for static, non-changing constants to distinguish them from enums, variables, or component names.  
   _Example:_

```typescript
const DEFAULT_PAGE_SIZE = 10;
const MAX_FILE_SIZE = 5 * 1024 * 1024;
  ```

5. **Styles File:**
   Create a SCSS style file with the same name as the component (PascalCase) and place it in the same folder as the component.

6. **Formatting Rules:**

   - Use 4 spaces or tabs for indentation.
   - Always use semicolons.
   - Use single quotes for strings.

7. **CSS Naming:**
   Use the **BEM (Block Element Modifier)** naming convention for CSS class names.

8. **Measurements:**
   Use `rem` units for font sizes, margins, and paddings to ensure scalability.

9. **Absolute Imports:**
   Use absolute imports for better readability and maintainability. Example:

   ```javascript
   import { Table } from '@components/table';
   ```

10. **Import Order:**
   Follow this order for organizing imports in files:

   - **First:** Third-party libraries (e.g., `react`, `react-dom`).
   - **Second:** Absolute imports (e.g., `@components`, `@utils`).
   - **Third:** Relative imports from the same project.
   - **Last:** Style imports.

### Creating Additional Components

- If additional components are needed, create a folder named `components`.
- Inside the `components` folder, organize components by function, each in its own folder.
- Name the folders according to the functionality of the component, using `kebab-case`.

### Example Structure

Here’s an example of a new component setup:

#### Folder: `new-component`

##### Component File: `NewComponent.tsx`

```tsx
// React Arrow Functional Component
const NewComponent = () => {
    const greeting = 'Hello Function Component!';

    return <h1>{greeting}</h1>;
};

export default NewComponent;
```

##### Style File: `NewComponent.scss`

```scss
.new-component {
    // BEM Naming Example
    &__title {
        font-size: 1.5rem;
        margin: 1rem 0;
    }
}
```

### Imports

```tsx
import { useCallback, useState } from 'react';
import { Breadcrumb } from '@components/breadcrumb';
import { Button } from '@components/button';
import { Table } from '@components/table';
import { ALL_DATA, SEARCH_KEYS, TABLE_FIELDS } from '.';
import './UserRoles.scss';
```


