import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    isOpen: true,
};

const sidebarSlice = createSlice({
    name: 'sidebar',
    initialState,
    reducers: {
        openSidebar: state => {
            state.isOpen = true;
        },
        toggleSidebar: state => {
            state.isOpen = !state.isOpen;
        },
    },
});
export const { openSidebar, toggleSidebar } = sidebarSlice.actions;
export default sidebarSlice.reducer;
