export interface IValidationService<TObject> {
    isValid(object: TObject): boolean;
}