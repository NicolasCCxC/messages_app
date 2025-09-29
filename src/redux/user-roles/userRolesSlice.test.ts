// src/redux/user-roles/userRolesSlice.test.ts
import reducer from './userRolesSlice';
import { getUserRoles, updateRole } from './actions';

describe('userRolesSlice', () => {
    it('estado inicial', () => {
        const state = reducer(undefined, { type: '@@INIT' } as any);
        expect(state).toHaveProperty('allData');
        expect(state).toHaveProperty('error');
        expect(state).toHaveProperty('pages');
        expect(state).toHaveProperty('message');
        expect(state.allData).toEqual([]);
        expect(state.error).toBe('');
        expect(state.pages).toBe(1);
        expect(state.message).toBe('');
    });

    describe('getUserRoles.fulfilled', () => {
        it('set allData cuando payload es un array', () => {
            const prev: any = { allData: [], error: 'err', pages: 0 };
            const payload = [{ id: 'r1' }];
            const next = reducer(prev, { type: getUserRoles.fulfilled.type, payload });
            expect(next.allData).toEqual(payload);
            expect(next.error).toBe('');
            expect(next.pages).toBe(0); // se mantiene
        });

        it('set allData y pages cuando payload es un objeto con content y totalPages', () => {
            const prev: any = { allData: [], error: 'err', pages: 1 };
            const payload = { content: [{ id: 'r2' }], totalPages: 5 };
            const next = reducer(prev, { type: getUserRoles.fulfilled.type, payload });
            expect(next.allData).toEqual(payload.content);
            expect(next.pages).toBe(5);
            expect(next.error).toBe('');
        });

        it('si payload es objeto sin content ni totalPages, deja allData vacío y no toca pages', () => {
            const prev: any = { allData: [{ id: 'x' }], error: 'err', pages: 3 };
            const payload = {};
            const next = reducer(prev, { type: getUserRoles.fulfilled.type, payload });
            expect(next.allData).toEqual([]); // usa ?? []
            expect(next.pages).toBe(3); // se mantiene
            expect(next.error).toBe('');
        });
    });

    describe('getUserRoles.rejected', () => {
        it('usa action.payload si existe', () => {
            const prev: any = { allData: [], error: '', pages: 1 };
            const next = reducer(prev, {
                type: getUserRoles.rejected.type,
                payload: 'custom-error',
            });
            expect(next.error).toBe('custom-error');
        });

        it('usa action.error.message si no hay payload', () => {
            const prev: any = { allData: [], error: '', pages: 1 };
            const next = reducer(prev, {
                type: getUserRoles.rejected.type,
                error: { message: 'from-error' },
            });
            expect(next.error).toBe('from-error');
        });

        it('usa fallback "Error" si no hay payload ni error', () => {
            const prev: any = { allData: [], error: '', pages: 1 };
            const next = reducer(prev, { type: getUserRoles.rejected.type });
            expect(next.error).toBe('Error');
        });
    });

    describe('updateRole.fulfilled', () => {
        it('reemplaza allData y setea message', () => {
            const prev: any = { allData: [{ id: 'A' }], error: 'err', pages: 1, message: '' };
            const payload = { data: [{ id: 'A*' }], message: 'ok' };
            const next = reducer(prev, { type: updateRole.fulfilled.type, payload });
            expect(next.allData).toEqual(payload.data);
            expect(next.message).toBe('ok');
            expect(next.error).toBe('');
        });

        it('mantiene allData si no hay data en el payload', () => {
            const prev: any = { allData: [{ id: 'B' }], error: 'err', pages: 1, message: 'old' };
            const payload = {};
            const next = reducer(prev, { type: updateRole.fulfilled.type, payload });
            expect(next.allData).toEqual(prev.allData);
            expect(next.message).toBe('');
            expect(next.error).toBe('');
        });
    });

    describe('updateRole.rejected', () => {
        it('usa action.payload si existe', () => {
            const prev: any = { allData: [], error: '', pages: 1 };
            const next = reducer(prev, {
                type: updateRole.rejected.type,
                payload: 'update-error',
            });
            expect(next.error).toBe('update-error');
        });

        it('usa action.error.message si no hay payload', () => {
            const prev: any = { allData: [], error: '', pages: 1 };
            const next = reducer(prev, {
                type: updateRole.rejected.type,
                error: { message: 'update-fail' },
            });
            expect(next.error).toBe('update-fail');
        });

        it('usa fallback "Error" si no hay payload ni error', () => {
            const prev: any = { allData: [], error: '', pages: 1 };
            const next = reducer(prev, { type: updateRole.rejected.type });
            expect(next.error).toBe('Error');
        });
    });
});
