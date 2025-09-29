import { Icon } from '@components/icon';
import { IHeaderField } from '@models/Table';

export const Header: React.FC<{ fields: IHeaderField[] }> = ({ fields }) => (
    <thead className="h-[2.3125rem] text-sm">
        <tr>
            {fields.map(({ value, className, icon }) =>
                value ? (
                    <th
                        className={`border border-b-0 border-gray text-white bg-blue-dark text-left px-2.5 py-[0.1563rem] ${className}`}
                        key={value}
                    >
                        {icon && <Icon name={icon} className="inline mr-2.5" />} {value}
                    </th>
                ) : null
            )}
        </tr>
    </thead>
);
