# Plan: Fix Vue Router Error on `/login` Route

## Problem
Route `/login` (and `/register`) in `src/router/index.js` only has `beforeEnter` guard, no `component`. Vue Router 4 requires every route to have `component` or `redirect`.

## Root Cause
```js
// src/router/index.js:14-19
{
  path: "/login",
  beforeEnter: () => {
    window.location.href = `${BACKEND}/login`;
  },
},
```

## Solution
Add a minimal dummy component to satisfy Vue Router. Since `beforeEnter` redirects before render, the dummy is never shown.

## Tasks

### 1. Fix `/login` and `/register` routes
**File**: `src/router/index.js`
- Import `defineComponent` from Vue (or use inline object)
- Add `component: { template: '<div></div>' }` to both routes
- Alternative: use `redirect: () => \`${BACKEND}/login\`` but needs BACKEND import

### 2. Verify local dev flow
- Run `php artisan serve --port=8000` in `backend/`
- Run `npm.cmd run dev` in root
- Test: click "SPMB" → redirect to `http://localhost:8000/login` → login → redirect back to `http://localhost:5174/?auth=...`

## Validation
- No `[VUE_ROUTER_R0004]` error in console
- Login flow works end-to-end locally

## Out of Scope
- Production `PROD_BACKEND` fix (separate issue)
- Backend session cookie config (separate issue)