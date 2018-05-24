import {
    GraphQLInputObjectType,
    GraphQLString,
    GraphQLList,
} from 'graphql';


export default new GraphQLInputObjectType({
    name: 'authorinput',
    fields: () => ({
        name: { type: GraphQLString },
        last_name: { type: GraphQLString },
        name: { type: GraphQLString }
    })
});