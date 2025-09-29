/* eslint-disable @typescript-eslint/no-explicit-any */
import { useTableOperations } from '../useTableOperations';

type Cell = { rowIndex:number; columnIndex:number; content:string; style:any; colSpan?:number }
type Col  = { label:string; style:any; colSpan?:number }

const makeBase = () => {
  const element:any = {
    header: { columns: [{ label:'A', style:{} }, { label:'B', style:{} }] as Col[] },
    body: {
      cells: [
        { rowIndex:0, columnIndex:0, content:'a0', style:{} },
        { rowIndex:0, columnIndex:1, content:'a1', style:{} },
        { rowIndex:1, columnIndex:0, content:'b0', style:{} },
        { rowIndex:1, columnIndex:1, content:'b1', style:{} },
      ] as Cell[],
    },
  };
  const rows:any[][] = [
    [{...element.body.cells[0]}, {...element.body.cells[1]}],
    [{...element.body.cells[2]}, {...element.body.cells[3]}],
  ];
  let current = element;
  const setElement = (e:any) => { current = e; };
  let _selectedCell: any = null;
  const setSelectedCell = (c:any) => { _selectedCell = c; };
  let _selectedCells: any[] = [];
  const setSelectedCells = (cs:any[]) => { _selectedCells = cs; };

  const api = useTableOperations({
    element: current,
    setElement,
    selectedCell: _selectedCell,
    setSelectedCell,
    selectedCells: _selectedCells,
    setSelectedCells,
    rows,
  });

  const getCurrent = () => current;
  const setSel = (sc:any) => (_selectedCell = sc);
  const setSels = (scs:any[]) => (_selectedCells = scs);
  const refresh = () =>
    useTableOperations({
      element: current,
      setElement,
      selectedCell: _selectedCell,
      setSelectedCell,
      selectedCells: _selectedCells,
      setSelectedCells,
      rows,
    });

  return { api, getCurrent, setSel, setSels, refresh };
};

describe('useTableOperations', () => {
  it('handleAddColumn agrega una columna y celdas por cada fila', () => {
    const S = makeBase();
    S.api.handleAddColumn();
    const st = S.getCurrent();
    expect(st.header.columns).toHaveLength(3);
    const newly = st.body.cells.filter((c:Cell) => c.columnIndex === 2);
    expect(newly).toHaveLength(2);
  });

  it('handleAddRow agrega una fila con tantas columnas como header', () => {
    const S = makeBase();
    S.api.handleAddRow();
    const st = S.getCurrent();
    const newRowCells = st.body.cells.filter((c:Cell) => c.rowIndex === 2);
    expect(newRowCells).toHaveLength(2);
  });

  it('handleUpdateHeaderCell actualiza label en header', () => {
    const S = makeBase();
    S.api.handleUpdateHeaderCell(1, 'Z');
    const st = S.getCurrent();
    expect(st.header.columns[1].label).toBe('Z');
  });

  it('handleUpdateBodyCell actualiza contenido en body', () => {
    const S = makeBase();
    S.api.handleUpdateBodyCell(1, 1, 'xx');
    const st = S.getCurrent();
    const c = st.body.cells.find((x:Cell) => x.rowIndex===1 && x.columnIndex===1);
    expect(c?.content).toBe('xx');
  });

  it('updateSelectedCellStyle → header (row=-1) y body', () => {
    const S = makeBase();

    S.setSel({ row:-1, column:1, style:{} });
    S.refresh().updateSelectedCellStyle({ bold:true });
    let st = S.getCurrent();
    expect(st.header.columns[1].style.bold).toBe(true);

    S.setSel({ row:0, column:0, style:{} });
    S.refresh().updateSelectedCellStyle({ italic:true });
    st = S.getCurrent();
    const c = st.body.cells.find((x:Cell)=>x.rowIndex===0 && x.columnIndex===0);
    expect(c?.style.italic).toBe(true);
  });

  it('handleMergeCells → merge header mismo row', () => {
    const S = makeBase();
    S.setSels([
      { row:-1, column:0, style:{a:1}, colSpan:1 },
      { row:-1, column:1, style:{b:2}, colSpan:1 },
    ]);
    S.refresh().handleMergeCells();
    const st = S.getCurrent();
    expect(st.header.columns[0].label).toBe('A B');
    expect(st.header.columns[0].colSpan).toBe(2);
    expect(st.header.columns.length).toBe(1);
  });

  it('handleMergeCells → merge body mismo row', () => {
    const S = makeBase();
    S.setSels([
      { row:0, column:0, style:{x:1}, colSpan:1 },
      { row:0, column:1, style:{y:2}, colSpan:1 },
    ]);
    S.refresh().handleMergeCells();
    const st = S.getCurrent();
    const base = st.body.cells.find((c:Cell)=>c.rowIndex===0 && c.columnIndex===0);
    const removed = st.body.cells.find((c:Cell)=>c.rowIndex===0 && c.columnIndex===1);
    expect(base?.content).toBe('a0 a1');
    expect(base?.colSpan).toBe(2);
    expect(removed).toBeUndefined();
  });

  it('handleMergeCells → split header cuando selectedCells tiene un solo item con colSpan>1', () => {
    const S = makeBase();
    S.setSels([
      { row:-1, column:0, style:{a:1}, colSpan:2 },
    ]);
    S.refresh().handleMergeCells();
    const st = S.getCurrent();
    expect(st.header.columns.length).toBe(3);
  });

  it('handleMergeCells → split body cuando un item tiene colSpan>1', () => {
    const S = makeBase();
    const base = S.getCurrent();
    base.body.cells = base.body.cells
      .filter((c:Cell)=>!(c.rowIndex===0 && c.columnIndex===1));
    base.body.cells = base.body.cells.map((c:Cell)=> c.rowIndex===0 && c.columnIndex===0 ? {...c, colSpan:2, style:{z:1}} : c );

    S.setSels([{ row:0, column:0, style:{z:1}, colSpan:2 }]);
    S.refresh().handleMergeCells();
    const st = S.getCurrent();
    const r0c0 = st.body.cells.find((c:Cell)=>c.rowIndex===0 && c.columnIndex===0);
    const r0c1 = st.body.cells.find((c:Cell)=>c.rowIndex===0 && c.columnIndex===1);
    expect(r0c0?.colSpan).toBe(1);
    expect(r0c1).toBeTruthy();
  });

  it('handleMergeCells → returns si vacío, o si no es mismo row', () => {
    const S = makeBase();
    S.setSels([]);
    S.refresh().handleMergeCells();
    const before = JSON.stringify(S.getCurrent());

    S.setSels([
      { row:0, column:0, style:{}, colSpan:1 },
      { row:1, column:1, style:{}, colSpan:1 },
    ]);
    S.refresh().handleMergeCells();
    const after = JSON.stringify(S.getCurrent());
    expect(before).toBe(after);
  });
});
