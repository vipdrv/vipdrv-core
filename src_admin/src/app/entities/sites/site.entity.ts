import { Entity } from "./../entity";
import { Variable } from "./../../utils/index";

export class SiteEntity extends Entity {
    userId: number;
    beautyId: string;
    name: string;
    url: string;
    contacts: string;

    constructor(id: string, userId: number, beautyId: string, name: string, url: string, contacts: string) {
        super(id);
        this.userId = userId;
        this.beautyId = beautyId;
        this.name = name;
        this.url = url;
        this.contacts = contacts;
    }

    static map(obj: any): SiteEntity {
        if (Variable.isNullOrUndefined(obj)) {
            return null;
        }
        let mock: SiteEntity = <SiteEntity>obj;
        return new SiteEntity(mock.id, mock.userId, mock.beautyId, mock.name, mock.url, mock.contacts);
    }
}