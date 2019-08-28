import { IEntity } from './i-entity';
export interface ILightEntity<TKey> extends IEntity<TKey> {
    displayText: string;
}