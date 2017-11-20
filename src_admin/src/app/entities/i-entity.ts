export interface IEntity<TKey> {
    id: TKey;
    initializeFromDto(dto: any);
}